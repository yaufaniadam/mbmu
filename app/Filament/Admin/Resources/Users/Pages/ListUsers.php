<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UsersResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ImportAction::make()
                ->label('Impor Data Pengguna')
                ->importer(\App\Filament\Imports\UserImporter::class)
                ->visible(fn () => true),
            CreateAction::make(),
        ];
    }
}
