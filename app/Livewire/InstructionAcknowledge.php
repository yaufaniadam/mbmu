<?php

namespace App\Livewire;


use App\Models\Instruction;
use App\Models\InstructionAcknowledgment;
use Filament\Notifications\Notification;
use Livewire\Component;

class InstructionAcknowledge extends Component
{
    public int $instructionId;
    public ?InstructionAcknowledgment $acknowledgment = null;

    public function mount()
    {
        $instruction = Instruction::findOrFail($this->instructionId);
        $this->acknowledgment = $instruction->getAcknowledgmentFor(auth()->id());
    }

    public function acknowledge()
    {
        try {
            $instruction = Instruction::findOrFail($this->instructionId);
            
            // Check if already acknowledged
            if ($instruction->isAcknowledgedBy(auth()->id())) {
                Notification::make()
                    ->title('Instruksi sudah dibaca sebelumnya')
                    ->warning()
                    ->send();
                return;
            }

            // Create acknowledgment
            $this->acknowledgment = InstructionAcknowledgment::create([
                'instruction_id' => $this->instructionId,
                'user_id' => auth()->id(),
                'acknowledged_at' => now(),
            ]);

            Notification::make()
                ->title('Terima kasih!')
                ->body('Konfirmasi pembacaan instruksi telah tersimpan.')
                ->success()
                ->send();

            // Emit event to refresh parent if needed
            $this->dispatch('instruction-acknowledged');
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Gagal menyimpan konfirmasi. Silakan coba lagi.')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.instruction-acknowledge');
    }
}
