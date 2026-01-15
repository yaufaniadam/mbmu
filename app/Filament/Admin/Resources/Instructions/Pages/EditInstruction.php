<?php

namespace App\Filament\Admin\Resources\Instructions\Pages;

use App\Filament\Admin\Resources\Instructions\InstructionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInstruction extends EditRecord
{
    protected static string $resource = InstructionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
