<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UsersResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUsers extends EditRecord
{
    protected static string $resource = UsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure sppg relationship is loaded
        $this->record->load('sppg', 'lembagaDipimpin');
        
        // Set lembaga_pengusul_id for the form
        $data['lembaga_pengusul_id'] = $this->record->lembagaDipimpin?->id;
        
        return $data;
    }

    protected function afterSave(): void
    {
        $lembagaPengusulId = $this->data['lembaga_pengusul_id'] ?? null;
        
        // If user has Pimpinan Lembaga Pengusul role, update the lembaga_pengusul table
        if ($this->record->hasRole('Pimpinan Lembaga Pengusul') && $lembagaPengusulId) {
            // Remove this user as pimpinan from any other lembaga
            \App\Models\LembagaPengusul::where('pimpinan_id', $this->record->id)
                ->where('id', '!=', $lembagaPengusulId)
                ->update(['pimpinan_id' => null]);
            
            // Set this user as pimpinan for the selected lembaga
            \App\Models\LembagaPengusul::where('id', $lembagaPengusulId)
                ->update(['pimpinan_id' => $this->record->id]);
        } else {
            // If not Pimpinan Lembaga Pengusul, remove from any lembaga
            \App\Models\LembagaPengusul::where('pimpinan_id', $this->record->id)
                ->update(['pimpinan_id' => null]);
        }
    }
}
