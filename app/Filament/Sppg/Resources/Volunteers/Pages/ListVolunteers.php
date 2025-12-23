<?php

namespace App\Filament\Sppg\Resources\Volunteers\Pages;

use App\Filament\Sppg\Resources\Volunteers\VolunteerResource;
use App\Filament\Imports\VolunteerImporter;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListVolunteers extends ListRecords
{
    protected static string $resource = VolunteerResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $sppgId = null;

        if ($user->hasRole('Kepala SPPG')) {
            $sppgId = User::find($user->id)->sppgDikepalai?->id;
        } else {
            $sppgId = User::find($user->id)->unitTugas->first()?->id;
        }

        return [
            ImportAction::make()
                ->importer(VolunteerImporter::class)
                ->options([
                    'sppg_id' => $sppgId,
                ]),
            CreateAction::make(),
        ];
    }
}
