<?php

namespace App\Filament\Admin\Resources\LembagaPengusuls\Pages;

use App\Filament\Admin\Resources\LembagaPengusuls\LembagaPengusulResource;
use App\Models\Sppg;
use Filament\Resources\Pages\CreateRecord;

class CreateLembagaPengusul extends CreateRecord
{
    protected static string $resource = LembagaPengusulResource::class;

    protected function afterCreate(): void
    {
        // 1. Get the form data
        $data = $this->form->getState();

        // 2. Check if there are SPPGs in the repeater
        if (! empty($data['sppgs'])) {

            // 3. Extract the IDs from the repeater array
            // The repeater structure is like: [['sppg_id' => 1], ['sppg_id' => 5]]
            $sppgIds = collect($data['sppgs'])
                ->pluck('sppg_id')
                ->filter(); // Remove nulls

            // 4. Update all those SPPGs to belong to this new Lembaga
            Sppg::whereIn('id', $sppgIds)->update([
                'lembaga_pengusul_id' => $this->record->id,
            ]);
        }
    }
}
