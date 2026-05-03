<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\LembagaPengusul;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('email')->label('Email')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lembagaDipimpin.nama_lembaga')
                    ->label('Nama Lembaga')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),
                TextColumn::make('sppgDiPj.lembagaPengusul.nama_lembaga')
                    ->label('Lembaga (via PJ)')
                    ->searchable()
                    ->hidden(),
                TextColumn::make('unitTugas.lembagaPengusul.nama_lembaga')
                    ->label('Lembaga (via Unit)')
                    ->searchable()
                    ->hidden(),
                TextColumn::make('roles.name')
                    ->label('Jabatan')
                    ->badge()
                    ->searchable(),
                TextColumn::make('sppg_column')
                    ->label('SPPG')
                    ->state(function (\App\Models\User $record): string {
                        if ($record->hasAnyRole(['Superadmin', 'Staf Kornas', 'Staf Akuntan Kornas', 'Ketua Kornas'])) {
                            return 'Kornas';
                        }

                        $sppgName = $record->sppgDiKepalai?->nama_sppg 
                            ?? $record->sppgDiPj?->nama_sppg
                            ?? $record->unitTugas->first()?->nama_sppg
                            ?? $record->sppg?->nama_sppg;

                        if ($sppgName) {
                            return $sppgName;
                        }

                        // Pimpinan Lembaga Pengusul tidak terkait SPPG unless they have another role
                        if ($record->hasRole('Pimpinan Lembaga Pengusul')) {
                            return '-';
                        }

                        return '-';
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Kornas' ? 'info' : 'success'),
                TextColumn::make('lembaga_pengusul_column')
                    ->label('Lembaga Pengusul')
                    ->state(function (\App\Models\User $record): string {
                        // Kornas tidak ada Lembaga Pengusul
                        if ($record->hasAnyRole(['Superadmin', 'Staf Kornas', 'Staf Akuntan Kornas', 'Ketua Kornas'])) {
                            return '-';
                        }

                        // Direct link (Pimpinan)
                        if ($record->hasRole('Pimpinan Lembaga Pengusul') && $record->lembagaDipimpin) {
                            return $record->lembagaDipimpin->nama_lembaga;
                        }

                        // Indirect link (SPPG Roles)
                        $managedSppg = $record->getManagedSppg();
                        if ($managedSppg && $managedSppg->lembagaPengusul) {
                            return $managedSppg->lembagaPengusul->nama_lembaga;
                        }

                        return '-';
                    })
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('roles')
                    ->label('Filter Jabatan')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
                \Filament\Tables\Filters\Filter::make('sppg_filter')
                    ->form([
                        \Filament\Forms\Components\Select::make('sppg_id')
                            ->label('Filter SPPG')
                            ->options(\App\Models\Sppg::pluck('nama_sppg', 'id'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['sppg_id'],
                            fn ($query, $sppgId) => $query->where(function ($q) use ($sppgId) {
                                $q->whereHas('unitTugas', fn ($sq) => $sq->where('sppg.id', $sppgId))
                                  ->orWhereHas('sppgDiKepalai', fn ($sq) => $sq->where('id', $sppgId))
                                  ->orWhereHas('sppgDiPj', fn ($sq) => $sq->where('id', $sppgId))
                                  ->orWhere('sppg_id', $sppgId);
                            })
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['sppg_id']) return null;
                        $sppg = \App\Models\Sppg::find($data['sppg_id']);
                        return 'SPPG: ' . ($sppg?->nama_sppg ?? 'Unknown');
                    }),
                \Filament\Tables\Filters\Filter::make('lembaga_filter')
                    ->form([
                        \Filament\Forms\Components\Select::make('lembaga_id')
                            ->label('Filter Lembaga Pengusul')
                            ->options(LembagaPengusul::pluck('nama_lembaga', 'id'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['lembaga_id'],
                            fn ($query, $lembagaId) => $query->where(function ($q) use ($lembagaId) {
                                $q->whereHas('lembagaDipimpin', fn ($sq) => $sq->where('id', $lembagaId))
                                  ->orWhereHas('sppgDiKepalai', fn ($sq) => $sq->where('lembaga_pengusul_id', $lembagaId))
                                  ->orWhereHas('sppgDiPj', fn ($sq) => $sq->where('lembaga_pengusul_id', $lembagaId))
                                  ->orWhereHas('unitTugas', fn ($sq) => $sq->where('lembaga_pengusul_id', $lembagaId))
                                  ->orWhereHas('sppg', fn ($sq) => $sq->where('lembaga_pengusul_id', $lembagaId));
                            })
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['lembaga_id']) return null;
                        $lembaga = LembagaPengusul::find($data['lembaga_id']);
                        return 'Lembaga Pengusul: ' . ($lembaga?->nama_lembaga ?? 'Unknown');
                    }),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('kirim_wa')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn () => auth()->user()->hasAnyRole(['Superadmin', 'Staf Kornas']))
                    // ->url(function (\App\Models\User $record) {
                    //     if (config('whatsapp.gateway') !== 'manual') return null;
                    //
                    //     $phone = preg_replace('/[^0-9]/', '', $record->telepon);
                    //     if (str_starts_with($phone, '0')) {
                    //         $phone = '62' . substr($phone, 1);
                    //     } elseif (str_starts_with($phone, '8')) {
                    //         $phone = '62' . $phone;
                    //     }
                    //
                    //     if (empty($phone)) return null;
                    //
                    //     $template = \App\Models\SystemSetting::getByKey('whatsapp_bulk_message', '');
                    //     $message = str_replace(["\r\n", "\r"], "\n", $template);
                    //
                    //     return app(\App\Services\WhatsAppService::class)->getManualUrl($phone, $message);
                    // })
                    // ->openUrlInNewTab()
                    ->requiresConfirmation()
                    ->action(function (\App\Models\User $record) {
                        // if (config('whatsapp.gateway') === 'manual') return;

                        $phone = preg_replace('/[^0-9]/', '', $record->telepon);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        } elseif (str_starts_with($phone, '8')) {
                            $phone = '62' . $phone;
                        }

                        if (empty($phone)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal!')
                                ->body('Nomor telepon tidak valid.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $templateRecord = \App\Models\NotificationTemplate::where('key', 'whatsapp_bulk_message')->first();
                        $template = $templateRecord?->content ?? '';
                        $message = str_replace(["\r\n", "\r"], "\n", $template);

                        // Personalization
                        $message = str_replace('{{name}}', $record->name, $message);

                        // Anti-Spam: Add unique suffix
                        $message .= "\n\n_" . now()->format('H:i') . "_" . rand(100, 999);

                        $result = app(\App\Services\WhatsAppService::class)->send($phone, $message);

                        if ($result['success']) {
                            \Filament\Notifications\Notification::make()
                                ->title('Berhasil!')
                                ->body($result['message'])
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal mengirim!')
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
