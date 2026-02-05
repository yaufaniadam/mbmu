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
use Illuminate\Database\Eloquent\Builder;

class KepalaSppgTokensTable
{
    public static function configure(Table $table, bool $showRoleFilter = true): Table
    {
        $filters = [
            SelectFilter::make('status_sppg')
                ->label('Status SPPG')
                ->options([
                    'Proses Persiapan' => 'Proses Persiapan',
                    'Verifikasi' => 'Verifikasi',
                    'Operasional' => 'Operasional',
                ])
                ->query(function (Builder $query, array $data) {
                    if (! $data['value']) {
                        return $query;
                    }
                    return $query->whereHas('sppg', function (Builder $query) use ($data) {
                        $query->where('status', $data['value']);
                    });
                }),
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
                    ->placeholder('-'),
                
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
                    ->label('Kirim Token')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Token via WhatsApp')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('recipient_phone')
                            ->label('Nomor Tujuan')
                            ->default(fn (RegistrationToken $record) => $record->recipient_phone)
                            ->required(),
                    ])
                    ->action(function (RegistrationToken $record, array $data) {
                        $record->update(['recipient_phone' => $data['recipient_phone']]);
                        try {
                            \Illuminate\Support\Facades\Notification::send($record, new \App\Notifications\KirimToken($record));
                            \Filament\Notifications\Notification::make()->title('Pesan WA Dikirim')->success()->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()->title('Gagal mengirim WA')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->visible(fn (RegistrationToken $record) => !$record->sppg?->kepalaSppg),
                Action::make('send_credentials')
                    ->label('Kirim Kridensial')
                    ->icon('heroicon-o-key')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Kridensial Login')
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
                            
                            if ($record->role !== 'kepala_sppg') {
                                throw new \Exception('Tabel ini khusus Token Kepala SPPG.');
                            }

                            if (!$sppg->kepalaSppg) {
                                throw new \Exception('Data Kepala SPPG tidak ditemukan. Silakan kirim Token Registrasi terlebih dahulu.');
                            }
                            $user = $sppg->kepalaSppg;
                            $password = \Illuminate\Support\Str::random(8);
                            
                            $user->password = \Illuminate\Support\Facades\Hash::make($password);
                            $user->save();

                            $notification = new \App\Notifications\KirimKridensialSppg($user, $password);
                            
                            \Illuminate\Support\Facades\Log::info("KirimKridensial Action ({$record->role}): Sending to " . $data['recipient_phone']);
                            \Illuminate\Support\Facades\Notification::route('WhatsApp', $data['recipient_phone'])->notify($notification);
                            \Filament\Notifications\Notification::make()->title('Kridensial Dikirim')->body("Password berhasil di-reset dan dikirim ke User: {$user->name}")->success()->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()->title('Gagal mengirim')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->visible(fn (RegistrationToken $record) => $record->sppg?->kepalaSppg),
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
