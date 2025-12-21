<?php

namespace App\Filament\Admin\Resources\LembagaPengusuls\Pages;

use App\Filament\Admin\Resources\LembagaPengusuls\LembagaPengusulResource;
use App\Models\Sppg;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLembagaPengusul extends EditRecord
{
    protected static string $resource = LembagaPengusulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Capture the virtual field data
        $this->sppgRepeaterItems = $data['sppgs'] ?? [];

        // Remove it from the array that gets sent to the database
        unset($data['sppgs']);

        return $data;
    }

    /**
     * STEP 2: SYNC RELATIONSHIPS
     * Now that the main record is saved safely, handle the relations.
     */
    protected function afterSave(): void
    {
        // Get the IDs meant to be linked
        $selectedSppgIds = collect($this->sppgRepeaterItems)
            ->pluck('sppg_id')
            ->filter()
            ->toArray();

        // A. Link new items
        if (! empty($selectedSppgIds)) {
            Sppg::whereIn('id', $selectedSppgIds)->update([
                'lembaga_pengusul_id' => $this->record->id,
            ]);
        }

        // B. Unlink removed items
        // "Find SPPGs belonging to this Lembaga that are NOT in the new list"
        Sppg::where('lembaga_pengusul_id', $this->record->id)
            ->whereNotIn('id', $selectedSppgIds)
            ->update([
                'lembaga_pengusul_id' => null,
            ]);
    }
}
