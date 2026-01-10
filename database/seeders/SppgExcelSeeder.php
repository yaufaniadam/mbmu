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
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
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

                // Find or create Lembaga Pengusul
                $lembagaPengusul = $this->findOrCreateLembagaPengusul($data);

                // Create SPPG
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

                $sppg = Sppg::create([
                    'nama_sppg' => trim($data['nama_sppg']),
                    'kode_sppg' => strtoupper(trim($data['kode_sppg'])),
                    'is_active' => $data['is_active'] === 'True' || $data['is_active'] === '1',
                    'status' => trim($data['status'] ?? '') ?: null,
                    'lembaga_pengusul_id' => $lembagaPengusul?->id,
                    'pj_id' => $pjUser?->id,
                    'alamat' => $alamat ?: 'Alamat belum diisi',
                ]);

                // Create registration token for Kepala SPPG
                RegistrationToken::create([
                    'token' => RegistrationToken::generateToken(),
                    'sppg_id' => $sppg->id,
                    'role' => 'kepala_sppg',
                    'max_uses' => 1,
                    'is_active' => true,
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

    protected function findOrCreateLembagaPengusul(array $data): ?LembagaPengusul
    {
       
        // Create a unique name based on pengusul + location
        $namaPengusul = $data['pengusul'];        

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

        // Assign "PJ Pelaksana" role
        $pjRole = Role::where('name', 'PJ Pelaksana')->first();
        if ($pjRole) {
            $user->assignRole($pjRole);
        }

        // // Send WA notification with credentials
        // try {
        //     $wablas = new WablasService();
        //     $message = "Selamat! Akun Anda sebagai PJ SPPG telah dibuat.\n\n"
        //         . "📱 Login: {$waPj}\n"
        //         . "🔐 Password: {$password}\n\n"
        //         . "Silakan login di panel SPPG untuk mengawasi operasional SPPG Anda.\n\n"
        //         . "Terima kasih!";
            
        //     $wablas->sendMessage($waPj, $message);
        // } catch (\Exception $e) {
        //     // Log error but don't fail seeder
        //     $this->command->warn("Failed to send WA to {$namaPj}: " . $e->getMessage());
        // }

        return $user;
    }
}
