<?php

namespace App\Filament\Admin\Resources\Instructions\Tables;

use App\Models\Instruction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstructionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                BadgeColumn::make('recipient_type')
                    ->label('Jenis Penerima')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'all' => 'Semua',
                        'role' => 'Jabatan',
                        'sppg' => 'SPPG',
                        'lembaga_pengusul' => 'Lembaga Pengusul',
                        'user' => 'Pengguna Tertentu',
                        default => $state,
                    })
                    ->colors([
                        'secondary' => 'all',
                        'success' => 'role',
                        'primary' => 'sppg',
                        'warning' => 'lembaga_pengusul',
                        'info' => 'user',
                    ]),
                
                TextColumn::make('recipient_names')
                    ->label('Penerima')
                    ->getStateUsing(fn (Instruction $record) => match ($record->recipient_type) {
                        'all' => 'Semua',
                        'role' => \Spatie\Permission\Models\Role::whereIn('id', $record->recipient_ids ?? [])->pluck('name')->join(', '),
                        'sppg' => \App\Models\Sppg::whereIn('id', $record->recipient_ids ?? [])->pluck('nama_sppg')->join(', '),
                        'lembaga_pengusul' => \App\Models\LembagaPengusul::whereIn('id', $record->recipient_ids ?? [])->pluck('nama_lembaga')->join(', '),
                        'user' => \App\Models\User::whereIn('id', $record->recipient_ids ?? [])->pluck('name')->join(', '),
                        default => '-',
                    })
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('whatsapp_status_summary')
                    ->label('Status WA')
                    ->state(function (Instruction $record) {
                        $stats = $record->whatsappMessages()
                            ->selectRaw('status, count(*) as count')
                            ->groupBy('status')
                            ->pluck('count', 'status');
                        
                        $count = [
                            'pending' => $stats['pending'] ?? 0,
                            'sent' => ($stats['sent'] ?? 0) + ($stats['read'] ?? 0) + ($stats['delivered'] ?? 0),
                            'failed' => $stats['failed'] ?? 0,
                        ];
                        
                        $parts = [];
                        if ($count['sent'] > 0) $parts[] = "<span class='text-success-600 font-bold'>✅ {$count['sent']}</span>";
                        if ($count['pending'] > 0) $parts[] = "<span class='text-gray-500'>⏳ {$count['pending']}</span>";
                        if ($count['failed'] > 0) $parts[] = "<span class='text-danger-600 font-bold'>❌ {$count['failed']}</span>";
                        
                        return empty($parts) ? '-' : implode(' | ', $parts);
                    })
                    ->html()
                    ->tooltip('Sent | Pending | Failed'),
                
                TextColumn::make('acknowledgment_rate')
                    ->label('Tingkat Dibaca')
                    ->getStateUsing(fn (Instruction $record) => $record->getAcknowledgmentRate() . '%')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query; // Can't sort by computed column easily
                    }),
                
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('recipient_type')
                    ->label('Jenis Penerima')
                    ->options([
                        'all' => 'Semua',
                        'role' => 'Jabatan',
                        'sppg' => 'SPPG',
                        'lembaga_pengusul' => 'Lembaga Pengusul',
                        'user' => 'Pengguna Tertentu',
                    ]),
                
                Filter::make('is_active')
                    ->label('Hanya Aktif')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->default(),
                
                SelectFilter::make('created_by')
                    ->label('Dibuat Oleh')
                    ->relationship('creator', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
