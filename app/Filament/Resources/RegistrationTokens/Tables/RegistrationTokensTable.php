<?php

namespace App\Filament\Resources\RegistrationTokens\Tables;

use App\Models\RegistrationToken;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class RegistrationTokensTable
{
    public static function configure(Table $table, bool $showRoleFilter = true): Table
    {
        $filters = [
            SelectFilter::make('is_active')
                ->label('Status')
                ->options([
                    '1' => 'Aktif',
                    '0' => 'Tidak Aktif',
                ]),
        ];

        if ($showRoleFilter) {
            array_unshift($filters, SelectFilter::make('role')
                ->label('Role')
                ->options(RegistrationToken::ROLE_LABELS));
        }

        return $table
            ->columns([
                TextColumn::make('sppg.nama_sppg')
                    ->label('SPPG')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sppg.status')
                    ->label('Status SPPG')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Proses Persiapan' => 'warning',
                        'Verifikasi' => 'info',
                        'Operasional' => 'success',
                        default => 'gray',
                    }),
                

                
                TextColumn::make('token')
                    ->label('Kode')
                    ->copyable()
                    ->copyMessage('Kode disalin!')
                    ->fontFamily('mono')
                    ->weight('bold'),
                
                TextColumn::make('recipient_name')
                    ->label('Penerima')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('recipient_phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('registration_url')
                    ->label('Link Registrasi')
                    ->state(fn (RegistrationToken $record): string => $record->getRegistrationUrl())
                    ->limit(30)
                    ->tooltip(fn (RegistrationToken $record): string => $record->getRegistrationUrl()),
                
                TextColumn::make('used_count')
                    ->label('Digunakan')
                    ->formatStateUsing(fn (RegistrationToken $record): string => "{$record->used_count}/{$record->max_uses}"),
                
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                
                TextColumn::make('expires_at')
                    ->label('Kadaluarsa')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Tidak ada')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters($filters)

            ->actions([
                EditAction::make(),
                Action::make('send_wa')
                    ->label('Kirim Token (Lama)')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Token via WhatsApp')
                    ->modalDescription(fn (RegistrationToken $record) => "Kirim token ke nomor WA: {$record->recipient_phone} ({$record->recipient_name})?")
                    ->form([
                        \Filament\Forms\Components\TextInput::make('recipient_phone')
                            ->label('Nomor Tujuan')
                            ->default(fn (RegistrationToken $record) => $record->recipient_phone)
                            ->required(),
                    ])
                    ->action(function (RegistrationToken $record, array $data) {
                        // Update phone number if changed in modal
                        $record->update(['recipient_phone' => $data['recipient_phone']]);
                        
                        try {
                            \Illuminate\Support\Facades\Notification::send($record, new \App\Notifications\KirimToken($record));
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Pesan WA Dikirim')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal mengirim WA')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(true),
                Action::make('send_credentials')
                    ->label('Kirim Kridensial')
                    ->icon('heroicon-o-key')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Kridensial Login')
                    ->modalDescription(fn (RegistrationToken $record) => "Kirim username & password baru ke WA: {$record->recipient_phone} ({$record->recipient_name})? \n\nPERINGATAN: Password pengguna akan di-reset!")
                    ->form([
                        \Filament\Forms\Components\TextInput::make('recipient_phone')
                            ->label('Nomor Tujuan')
                            ->default(fn (RegistrationToken $record) => $record->recipient_phone)
                            ->required(),
                    ])
                    ->action(function (RegistrationToken $record, array $data) {
                        try {
                            $record->update(['recipient_phone' => $data['recipient_phone']]);
                            
                            $sppg = $record->sppg;
                            if (!$sppg) {
                                throw new \Exception('SPPG tidak ditemukan.');
                            }

                            $user = null;
                            $notification = null;

                            // Generate new password
                            $password = \Illuminate\Support\Str::random(8); 

                            // Determine target user and notification based on role
                            if ($record->role === 'kepala_lembaga') {
                                if (!$sppg->lembagaPengusul || !$sppg->lembagaPengusul->pimpinan) {
                                    throw new \Exception('Data Kepala Lembaga Pengusul tidak ditemukan.');
                                }
                                $user = $sppg->lembagaPengusul->pimpinan;
                                $notification = new \App\Notifications\KirimKridensial($user, $password);
                            } elseif ($record->role === 'kepala_sppg') {
                                if (!$sppg->kepalaSppg) {
                                    throw new \Exception('Data Kepala SPPG tidak ditemukan. Pastikan SPPG sudah memiliki Kepala SPPG.');
                                }
                                $user = $sppg->kepalaSppg;
                                $notification = new \App\Notifications\KirimKridensialSppg($user, $password);
                            } else {
                                // Fallback or throw error if role is not supported for credentials
                                throw new \Exception('Role ini tidak mendukung pengiriman kridensial, atau fitur belum diimplementasikan.');
                            }
                            
                            // Reset Password
                            $user->password = \Illuminate\Support\Facades\Hash::make($password);
                            $user->save();

                            // Send Notification
                            \Illuminate\Support\Facades\Log::info("KirimKridensial Action ({$record->role}): Sending to " . $data['recipient_phone']);
                            
                            \Illuminate\Support\Facades\Notification::route('WhatsApp', $data['recipient_phone'])
                                ->notify($notification);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Kridensial Dikirim')
                                ->body("Password berhasil di-reset dan dikirim ke User: {$user->name} ({$record->role})")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal mengirim')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Tables\Actions\BulkAction::make('bulk_send_wa')
                        ->label('Kirim WA Massal')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->requiresConfirmation()
                        ->hidden() // Hide for now as logic is different
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                             // existing code hidden
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
