<?php

namespace App\Filament\Resources\RegistrationTokens\Tables;

use App\Models\RegistrationToken;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;

class PimpinanTokensTable
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
                TextColumn::make('sppg.lembagaPengusul.nama_lembaga')
                    ->label('Nama Lembaga Pengusul')
                    ->searchable()
                    ->sortable(),
                
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

                TextColumn::make('recipient_name')
                    ->label('Nama Penerima')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false), // Show by default for Pimpinan

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
                // Action 'send_wa' removed as per request (khusus kridensial)
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
                            if (!$sppg) throw new \Exception('SPPG tidak ditemukan.');

                            $user = null;
                            $notification = null;
                            $password = \Illuminate\Support\Str::random(8); 

                            if ($record->role === 'kepala_lembaga') {
                                if (!$sppg->lembagaPengusul || !$sppg->lembagaPengusul->pimpinan) {
                                    throw new \Exception('Data Pimpinan Lembaga Pengusul tidak ditemukan.');
                                }
                                $user = $sppg->lembagaPengusul->pimpinan;
                                $notification = new \App\Notifications\KirimKridensial($user, $password);
                            } else {
                                // Should not happen in Pimpinan Table but good specific check
                                throw new \Exception('Tabel ini khusus Token Pimpinan.');
                            }
                            
                            $user->password = \Illuminate\Support\Facades\Hash::make($password);
                            $user->save();

                            \Illuminate\Support\Facades\Log::info("KirimKridensial Action ({$record->role}): Sending to " . $data['recipient_phone']);
                            \Illuminate\Support\Facades\Notification::route('WhatsApp', $data['recipient_phone'])->notify($notification);
                            \Filament\Notifications\Notification::make()->title('Kridensial Dikirim')->body("Password berhasil di-reset dan dikirim ke User: {$user->name}")->success()->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()->title('Gagal mengirim')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->visible(true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
             ->headerActions([
                Action::make('manage_templates')
                    ->label('Atur Template Pesan')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->url(\App\Filament\Resources\NotificationTemplateResource::getUrl())
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
