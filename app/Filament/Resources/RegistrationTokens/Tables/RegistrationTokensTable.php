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
                \Filament\Actions\Action::make('send_wa')
                    ->label('Kirim WA')
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('bulk_send_wa')
                        ->label('Kirim WA Massal')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            \Illuminate\Support\Facades\Notification::send($records, new \App\Notifications\KirimToken($records->first())); // Notification::send handles collection, but we need to pass individual instance to constructor? NO. 
                            // Laravel Notification::send($collection, $notification) sends the SAME notification instance to all.
                            // But our KirimToken constructor takes a $token. If we pass one token, the message for ALL users will show that ONE token's data. 
                            // So for personalized messages, we must loop.
                            
                            $sent = 0;
                            foreach ($records as $record) {
                                if (empty($record->recipient_phone)) continue;
                                
                                try {
                                    $record->notify(new \App\Notifications\KirimToken($record));
                                    $sent++;
                                } catch (\Exception $e) {
                                    // log error
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
