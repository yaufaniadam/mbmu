<?php

namespace App\Filament\Lembaga\Resources\InstructionResource\Pages;

use App\Filament\Lembaga\Resources\InstructionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewInstruction extends ViewRecord
{
    protected static string $resource = InstructionResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];
        
        if ($this->record->attachment_path) {
             $url = Storage::disk('public')->url($this->record->attachment_path);
             
             $actions[] = Action::make('download')
                ->label('Download Lampiran')
                ->icon('heroicon-o-arrow-down-tray')
                ->url($url)
                ->openUrlInNewTab();
        }
        
        return $actions;
    }
}
