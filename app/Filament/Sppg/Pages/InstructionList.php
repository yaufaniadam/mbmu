<?php

namespace App\Filament\Sppg\Pages;

use App\Models\Instruction;
use App\Models\InstructionAcknowledgment;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class InstructionList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Instruksi';

    protected static ?int $navigationSort = 2;

    protected ?string $heading = 'Instruksi';
    
    protected string $view = 'filament.sppg.pages.instruction-list';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Instruction::query()
                    ->active()
                    ->forUser(auth()->user())
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->description(fn (Instruction $record): string => strip_tags(substr($record->content, 0, 100)) . '...')
                    ->wrap(),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (Instruction $record): string {
                        return $record->isAcknowledgedBy(auth()->id()) ? 'Sudah Dibaca' : 'Belum Dibaca';
                    })
                    ->colors([
                        'success' => 'Sudah Dibaca',
                        'warning' => 'Belum Dibaca',
                    ]),
                
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Instruction $record) => $record->title)
                    ->modalContent(fn (Instruction $record) => view('filament.sppg.pages.instruction-detail', [
                        'instruction' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('3xl'),
            ])
            ->paginated([10, 25, 50]);
    }
}
