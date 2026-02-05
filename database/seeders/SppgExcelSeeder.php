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
        $csvPath = database_path('seeders/data/sppg_test.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);
            return;
        }

        $handle = fopen($csvPath, 'r');
        // Use semicolon as delimiter since the file format has changed
        $header = fgetcsv($handle, 0, ';');
        
        $imported = 0;
        $skipped = 0;
        $tokensCreated = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                // Ensure row count matches header count to prevent array_combine errors
                if (count($row) !== count($header)) {
                    $this->command->warn("Row skipped due to column mismatch.");
                    continue;
                }

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
                // Pass full data array to access nama_sppg
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

                // Create or find Kepala SPPG user
                $kepalaSppgUser = null;
                if (!empty($data['ka_sppg']) && !empty($data['wa_ka_sppg'])) {
                    $kepalaSppgUser = $this->findOrCreateKepalaSppgUser($data);
                }

                // CHECK: If Kepala SPPG User is already assigned to another SPPG, we cannot assign them again due to unique constraint
                $kepalaSppgIdToAssign = $kepalaSppgUser?->id;
                if ($kepalaSppgUser && $kepalaSppgUser->sppgDiKepalai()->exists()) {
                    $existingSppg = $kepalaSppgUser->sppgDiKepalai;
                    $this->command->warn("User {$kepalaSppgUser->name} ({$kepalaSppgUser->telepon}) sudah menjadi Kepala SPPG di '{$existingSppg->nama_sppg}'. Tidak ditambahkan sebagai Kepala di '{$data['nama_sppg']}'.");
                    $kepalaSppgIdToAssign = null;
                }

                // Create SPPG
                $sppg = Sppg::create([
                    'nama_sppg' => trim($data['nama_sppg']),
                    'kode_sppg' => strtoupper(trim($data['kode_sppg'])),
                    'is_active' => filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN),
                    'status' => trim($data['status'] ?? '') ?: null,
                    'lembaga_pengusul_id' => $lembagaPengusul->id,
                    'pj_id' => $pjUser?->id,
                    'kepala_sppg_id' => $kepalaSppgIdToAssign,
                    'alamat' => $alamat ?: 'Alamat belum diisi',
                    'province_code' => $province?->code,
                    'city_code' => $city?->code,
                    'district_code' => $district?->code,
                ]);

                // 1. Create Token for Kepala SPPG
                $kaSppgName = $kepalaSppgUser?->name ?? $data['ka_sppg'] ?? null;
                $kaSppgPhone = $kepalaSppgUser?->telepon ?? (isset($data['wa_ka_sppg']) ? $this->normalizePhone($data['wa_ka_sppg']) : null);
                
                if (!empty($kaSppgPhone)) {
                    RegistrationToken::create([
                        'token' => RegistrationToken::generateToken(),
                        'sppg_id' => $sppg->id,
                        'role' => 'kepala_sppg',
                        'max_uses' => 1,
                        'is_active' => true,
                        'recipient_name' => $kaSppgName,
                        'recipient_phone' => $kaSppgPhone,
                    ]);
                    $tokensCreated++;
                }

                // 2. Create Token for Kepala Lembaga (PJ)
                // Always create this if PJ info exists, as they need to register/manage the Lembaga aspect
                $pjName = $pjUser?->name ?? $data['nama_pj'] ?? null;
                $pjPhone = $pjUser?->telepon ?? (isset($data['wa_pj']) ? $this->normalizePhone($data['wa_pj']) : null);

                if (!empty($pjPhone)) {
                     // Check if we already created a token for this Lembaga Pengusul in this run
                     // Or theoretically check DB, but in this seeder context, deduping per run is key.
                     // Note: We prefer to check if a token for this Lembaga already exists to be safe.
                     $existingToken = RegistrationToken::whereHas('sppg', function($q) use ($lembagaPengusul) {
                        $q->where('lembaga_pengusul_id', $lembagaPengusul->id);
                     })->where('role', 'kepala_lembaga')->exists();

                     if (!$existingToken) {
                         RegistrationToken::create([
                            'token' => RegistrationToken::generateToken(),
                            'sppg_id' => $sppg->id,
                            'role' => 'kepala_lembaga',
                            'max_uses' => 1,
                            'is_active' => true,
                            'recipient_name' => $pjName,
                            'recipient_phone' => $pjPhone,
                        ]);
                        $tokensCreated++;
                     }
                }

                $imported++;
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
        $namaPengusulRaw = trim($data['pengusul'] ?? '');
        $namaSppg = trim($data['nama_sppg'] ?? '');
        
        if (empty($namaPengusulRaw)) {
            return null;
        }

        // Combined Name: "Pengusul - Nama SPPG"
        // e.g., "PDM Kota Serang - SMP Birrul Walidain"
        $namaLembaga = $namaPengusulRaw . ' - ' . $namaSppg;

        // Find existing or create new
        return LembagaPengusul::firstOrCreate(
            ['nama_lembaga' => $namaLembaga],
            [
                'alamat_lembaga' => trim($data['kab_kota'] ?? '') . ', ' . trim($data['provinsi'] ?? ''),
            ]
        );
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading 0 and replace with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } 
        // If starts with 8, add 62
        elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }
        // If not starting with 62, prepend it (safety check, though careful with short numbers)
        elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    protected function findOrCreatePjUser(array $data): ?User
    {
        $namaPj = trim($data['nama_pj']);
        // Normalize phone to 62 format
        $waPj = $this->normalizePhone(trim($data['wa_pj']));

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

        // Assign "Pimpinan Lembaga Pengusul" (Perwakilan Yayasan) AND "PJ Pelaksana" roles
        // This ensures they have full access to Admin Panel (as Pimpinan) and Operational features (as PJ)
        
        $pimpinanRole = Role::where('name', 'Pimpinan Lembaga Pengusul')->first();
        if ($pimpinanRole) {
            $user->assignRole($pimpinanRole);
        }

        $pjRole = Role::where('name', 'PJ Pelaksana')->first();
        if ($pjRole) {
            $user->assignRole($pjRole);
        }

        // Link User to Lembaga Pengusul if not already linked
        // NOTE: Since Lembaga Pengusul names are now unique per SPPG, this logic might need adjustment if users reuse the SAME user for multiple Lembagas.
        // But assuming 1 user -> 1 Pimpinan for now, or just setting it if empty.
        // Re-fetching the specific lembaga created/found above is safer if we passed it in, but here we construct name again.
        $namaPengusulRaw = trim($data['pengusul'] ?? '');
        $namaSppg = trim($data['nama_sppg'] ?? '');
        $targetLembagaName = $namaPengusulRaw . ' - ' . $namaSppg;

        $lembaga = LembagaPengusul::where('nama_lembaga', $targetLembagaName)->first();
        if ($lembaga && !$lembaga->pimpinan_id) {
            $lembaga->update(['pimpinan_id' => $user->id]);
        }

        return $user;
    }

    protected function findOrCreateKepalaSppgUser(array $data): ?User
    {
        $nama = trim($data['ka_sppg']);
        $wa = $this->normalizePhone(trim($data['wa_ka_sppg']));

        if (empty($nama) || empty($wa)) {
            return null;
        }

        // Check if user already exists
        $existingUser = User::where('telepon', $wa)->first();
        if ($existingUser) {
            // Check if they have the role, if not assign it
            if (!$existingUser->hasRole('Kepala SPPG')) {
                $existingUser->assignRole('Kepala SPPG');
            }
            return $existingUser;
        }

        $password = 'mbm' . substr($wa, -4);

        $user = User::create([
            'name' => $nama,
            'telepon' => $wa,
            'password' => Hash::make($password),
        ]);

        $role = Role::where('name', 'Kepala SPPG')->first();
        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }
}
