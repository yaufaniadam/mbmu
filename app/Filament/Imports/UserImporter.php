<?php

namespace App\Filament\Imports;

use App\Models\User;
use App\Models\Sppg;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Number;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('role')
                ->label('Jabatan')
                ->requiredMapping()
                ->fillRecordUsing(function (User $record, $state) {
                    // Do nothing - handled in afterSave
                })
                ->rules(['required']),
            ImportColumn::make('no_wa')
                ->label('No WA')
                ->requiredMapping()
                ->fillRecordUsing(function (User $record, $state) {
                    // Do nothing - handled in resolveRecord
                })
                ->rules(['required']),
            ImportColumn::make('id_sppg')
                ->label('ID SPPG')
                ->requiredMapping()
                ->fillRecordUsing(function (User $record, $state) {
                    // Do nothing - handled in afterSave
                })
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ?User
    {
        $phone = trim($this->data['no_wa'] ?? '');
        
        // Normalize phone number (handle 0, 62, and stripping non-digits)
        // Convert 08... to 628... or similar standard
        $normalizedPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($normalizedPhone, '0')) {
            $normalizedPhone = '62' . substr($normalizedPhone, 1);
        } elseif (str_starts_with($normalizedPhone, '8')) {
            $normalizedPhone = '62' . $normalizedPhone;
        }

        // Search for existing user using both raw and normalized phone
        $user = User::where('telepon', $phone)
            ->orWhere('telepon', $normalizedPhone)
            ->first() ?? new User();

        $user->telepon = $normalizedPhone; // Standardize to normalized format
        $user->name = $this->data['name'] ?? '';
        
        if (blank($user->email)) {
            $user->email = $normalizedPhone . '@mbmu.id';
        }

        $lastFourDigits = substr($normalizedPhone, -4);
        $user->password = 'mbm' . $lastFourDigits;

        return $user;
    }

    public function beforeSave(): void
    {
        // Safety hook to prevent SQLSTATE[42S22] "Unknown column" errors.
        // Even though we use fillRecordUsing(fn() => null), we unset them 
        // here at the last second to be technically certain they aren't saved.
        unset($this->record->role);
        unset($this->record->no_wa);
        unset($this->record->id_sppg);
        
        // Also clean up any potential historical collision names
        unset($this->record->import_jabatan);
        unset($this->record->import_no_wa);
        unset($this->record->import_id_sppg);
    }

    protected function afterSave(): void
    {
        $user = $this->record;
        $roleName = trim($this->data['role'] ?? '');
        $sppgInput = trim($this->data['id_sppg'] ?? '');

        // 1. Resolve SPPG by Numeric ID, Code (kode_sppg), or Name (nama_sppg)
        $sppg = null;
        if (is_numeric($sppgInput)) {
            $sppg = Sppg::find($sppgInput);
        }
        
        if (! $sppg) {
            $sppg = Sppg::where('kode_sppg', $sppgInput)->first();
        }

        if (! $sppg) {
            $sppg = Sppg::where('nama_sppg', $sppgInput)->first();
        }

        // 2. Assign Role
        if ($roleName) {
            $user->syncRoles([$roleName]);
        }
        
        // 3. Link User to SPPG (General Link)
        if ($sppg) {
            $user->update(['sppg_id' => $sppg->id]);

            // 4. Logic for Kepala SPPG or PJ Pelaksana (Management Link)
            if (in_array($roleName, ['Kepala SPPG', 'PJ Pelaksana'])) {
                if ($roleName === 'Kepala SPPG') {
                    $sppg->update(['kepala_sppg_id' => $user->id]);
                } elseif ($roleName === 'PJ Pelaksana') {
                    $sppg->update(['pj_id' => $user->id]);
                }
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data pengguna telah selesai dan ' . Number::format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimpor.';
        }

        return $body;
    }
}
