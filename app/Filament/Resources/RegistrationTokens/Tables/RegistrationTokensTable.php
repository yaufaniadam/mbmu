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

class RegistrationTokensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sppg.nama_sppg')
                    ->label('SPPG')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('role')
                    ->label('Role')
                    ->formatStateUsing(fn (string $state): string => RegistrationToken::ROLE_LABELS[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kepala_sppg' => 'primary',
                        'ahli_gizi' => 'success',
                        'akuntan' => 'warning',
                        'administrator' => 'info',
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
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(RegistrationToken::ROLE_LABELS),
                
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('send_wa')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Token via WhatsApp')
                    ->modalDescription(fn (RegistrationToken $record) => "Kirim token ke nomor WA: {$record->recipient_phone} ({$record->recipient_name})?")
                    ->form([
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->label('Nomor Tujuan')
                            ->default(fn (RegistrationToken $record) => $record->recipient_phone)
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('message_template')
                            ->label('Pesan')
                            ->default(fn (RegistrationToken $record) => "Assalamualaikum {$record->recipient_name},\n\n"
                                . "Silakan login ke MBMu App lalu buat akun.\n\n"
                                . "👉 Link: {$record->getRegistrationUrl()}\n"
                                . "🔑 Token: {$record->token}\n\n"
                                . "Gunakan link dan token di atas untuk mendaftar sebagai {$record->role_label} di {$record->sppg->nama_sppg}.\n\n"
                                . "Terima Kasih.")
                            ->rows(6)
                            ->required(),
                    ])
                    ->action(function (RegistrationToken $record, array $data) {
                        $wablas = new \App\Services\WablasService();
                        $message = $data['message_template'];
                        
                        if ($wablas->sendMessage($data['phone'], $message)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Pesan WA Terkirim')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal mengirim WA')
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('bulk_send_wa')
                        ->label('Kirim WA Massal')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            \Filament\Forms\Components\Textarea::make('message_template')
                                ->label('Template Pesan')
                                ->helperText('Gunakan {name}, {link}, {token}, {role}, {sppg} sebagai placeholder.')
                                ->default("Assalamualaikum {name},\n\n"
                                    . "Silakan login ke MBMu App lalu buat akun.\n\n"
                                    . "👉 Link: {link}\n"
                                    . "🔑 Token: {token}\n\n"
                                    . "Gunakan link dan token di atas untuk mendaftar sebagai {role} di {sppg}.\n\n"
                                    . "Terima Kasih.")
                                ->rows(6)
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $wablas = new \App\Services\WablasService();
                            $sent = 0;
                            foreach ($records as $record) {
                                if (empty($record->recipient_phone)) continue;
                                
                                $message = str_replace(
                                    ['{name}', '{link}', '{token}', '{role}', '{sppg}'],
                                    [$record->recipient_name, $record->getRegistrationUrl(), $record->token, $record->role_label, $record->sppg->nama_sppg],
                                    $data['message_template']
                                );

                                if ($wablas->sendMessage($record->recipient_phone, $message)) {
                                    $sent++;
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title("{$sent} Pesan WA Terkirim")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
