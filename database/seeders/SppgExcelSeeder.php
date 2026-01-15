<?php

namespace Database\Seeders;

use App\Models\LembagaPengusul;
use App\Models\RegistrationToken;
use App\Models\Sppg;
use App\Models\User;
use App\Services\WablasService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Spatie\Permission\Models\Role;

class SppgExcelSeeder extends Seeder
{
    /**
     * Map pengusul type to lembaga pengusul category
     */
    protected array $pengusulMap = [
        'PDM' => 'PDM',
        'PWM' => 'PWM',
        'PCA' => 'PCA',
        'PCM' => 'PCM',
        'PRM' => 'PRM',
        'Sekolah' => 'Sekolah',
        'Pesantren' => 'Pesantren',
        'Majelis/Lembaga PP' => 'PP Muhammadiyah',
    ];

    public function run(): void
    {
        $csvPath = database_path('seeders/data/sppg_excel_import.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);
            return;
        }

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle);
        
        $imported = 0;
        $skipped = 0;
        $tokensCreated = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);
                
                // Skip if no nama_sppg or kode_sppg
                if (empty($data['nama_sppg']) || empty($data['kode_sppg'])) {
                    $skipped++;
                    continue;
                }

                // Check if SPPG already exists
                $existingSppg = Sppg::where('kode_sppg', $data['kode_sppg'])->first();
                if ($existingSppg) {
                    $this->command->warn("SPPG with kode {$data['kode_sppg']} already exists, skipping.");
                    $skipped++;
                    continue;
                }

                // Normalize Phone in Data Source (for PJ creation)
                if (isset($data['wa_pj']) && str_starts_with(trim($data['wa_pj']), '8')) {
                    $data['wa_pj'] = '0' . trim($data['wa_pj']);
                }

                // Find or create Lembaga Pengusul
                $lembagaPengusul = $this->findOrCreateLembagaPengusul($data);
                
                // IMPORTANT: Skip SPPG creation if Lembaga Pengusul is not found to prevent orphan data
                if (!$lembagaPengusul) {
                    $this->command->warn("SPPG '{$data['nama_sppg']}' skipped: Kolom 'pengusul' kosong atau tidak valid.");
                    $skipped++;
                    continue;
                }

                // 1. Map Location Names to Codes
                $province = $this->findLocation(Province::class, $data['provinsi'] ?? '');
                $city = $this->findLocation(City::class, $data['kab_kota'] ?? '', $province?->code);
                $district = $this->findLocation(District::class, $data['kecamatan'] ?? '', $city?->code);

                // Create Address String
                $alamat = trim(implode(', ', array_filter([
                    $data['kecamatan'] ?? '',
                    $data['kab_kota'] ?? '',
                    $data['provinsi'] ?? '',
                ])));

                // Create or find PJ user
                $pjUser = null;
                if (!empty($data['nama_pj']) && !empty($data['wa_pj'])) {
                    $pjUser = $this->findOrCreatePjUser($data);
                }

                // Create SPPG
                $sppg = Sppg::create([
                    'nama_sppg' => trim($data['nama_sppg']),
                    'kode_sppg' => strtoupper(trim($data['kode_sppg'])),
                    'is_active' => filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN),
                    'status' => trim($data['status'] ?? '') ?: null,
                    'lembaga_pengusul_id' => $lembagaPengusul->id,
                    'pj_id' => $pjUser?->id,
                    'alamat' => $alamat ?: 'Alamat belum diisi',
                    'province_code' => $province?->code,
                    'city_code' => $city?->code,
                    'district_code' => $district?->code,
                ]);

                // Create registration token for Kepala SPPG
                RegistrationToken::create([
                    'token' => RegistrationToken::generateToken(),
                    'sppg_id' => $sppg->id,
                    'role' => 'kepala_sppg',
                    'max_uses' => 1,
                    'is_active' => true,
                    'recipient_name' => $pjUser?->name ?? $data['nama_pj'] ?? null,
                    'recipient_phone' => $pjUser?->telepon ?? isset($data['wa_pj']) ? ('0' . ltrim($data['wa_pj'], '0')) : null,
                ]);

                $imported++;
                $tokensCreated++;
            }

            DB::commit();
            
            $this->command->info("Import completed:");
            $this->command->info("- SPPG imported: {$imported}");
            $this->command->info("- Registration tokens created: {$tokensCreated}");
            $this->command->info("- Skipped: {$skipped}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Import failed: ' . $e->getMessage());
            throw $e;
        }

        fclose($handle);
    }

    protected function findLocation($modelClass, $name, $parentCode = null)
    {
        if (empty($name)) return null;
        $name = trim($name);
        
        $query = $modelClass::where('name', 'LIKE', "%{$name}%");
        
        if ($parentCode) {
            if ($modelClass === City::class) {
                $query->where('province_code', $parentCode);
            } elseif ($modelClass === District::class) {
                $query->where('city_code', $parentCode);
            }
        }
        
        return $query->first();
    }

    protected function findOrCreateLembagaPengusul(array $data): ?LembagaPengusul
    {
        // Validate pengusul field is not empty
        $namaPengusul = trim($data['pengusul'] ?? '');
        
        if (empty($namaPengusul)) {
            return null;
        }

        // Find existing or create new
        return LembagaPengusul::firstOrCreate(
            ['nama_lembaga' => $namaPengusul],
            [
                'alamat_lembaga' => trim($data['kab_kota'] ?? '') . ', ' . trim($data['provinsi'] ?? ''),
            ]
        );
    }

    protected function findOrCreatePjUser(array $data): ?User
    {
        $namaPj = trim($data['nama_pj']);
        $waPj = trim($data['wa_pj']);

        if (empty($namaPj) || empty($waPj)) {
            return null;
        }

        // Check if user already exists by phone number
        $existingUser = User::where('telepon', $waPj)->first();
        if ($existingUser) {
            // User exists, just return
            return $existingUser;
        }

        // Generate password: mbm + last 4 digits of phone
        $password = 'mbm' . substr($waPj, -4);

        // Create new user
        $user = User::create([
            'name' => $namaPj,
            'telepon' => $waPj,
            'password' => Hash::make($password),
        ]);

        // Assign "Pimpinan Lembaga Pengusul" role
        $pjRole = Role::where('name', 'Pimpinan Lembaga Pengusul')->first();
        if ($pjRole) {
            $user->assignRole($pjRole);
        }

        return $user;
    }
}
