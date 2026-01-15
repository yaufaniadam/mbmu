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
                
                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->sortable(),
                
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
