<?php

namespace App\Livewire;

use App\Models\Instruction;
use App\Models\InstructionAcknowledgment;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class InstructionAcknowledge extends Component implements HasForms
{
    use InteractsWithForms;

    public int $instructionId;
    public ?array $data = [];

    public function mount(int $instructionId)
    {
        $this->instructionId = $instructionId;
        $instruction = Instruction::findOrFail($instructionId);
        
        $this->form->fill([
            'is_acknowledged' => $instruction->isAcknowledgedBy(auth()->id()),
        ]);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Toggle::make('is_acknowledged')
                    ->label('Konfirmasi bahwa Anda telah membaca instruksi ini')
                    ->onColor('success')
                    ->offColor('danger') // Or gray
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->handleAcknowledgment($state);
                    }),
            ])
            ->statePath('data');
    }

    public function handleAcknowledgment(bool $state)
    {
        $instruction = Instruction::findOrFail($this->instructionId);
        $userId = auth()->id();

        if ($state) {
            // Check if already acknowledged
            if (!$instruction->isAcknowledgedBy($userId)) {
                InstructionAcknowledgment::create([
                    'instruction_id' => $this->instructionId,
                    'user_id' => $userId,
                    'acknowledged_at' => now(),
                ]);

                Notification::make()
                    ->title('Terima kasih!')
                    ->body('Konfirmasi pembacaan instruksi telah tersimpan.')
                    ->success()
                    ->send();
            }
        } else {
            // If toggled off, remove acknowledgment (optional, but logical for a toggle)
            $ack = $instruction->getAcknowledgmentFor($userId);
            if ($ack) {
                $ack->delete();
                 Notification::make()
                    ->title('Konfirmasi dibatalkan')
                    ->warning()
                    ->send();
            }
        }

        // Dispatch event to refresh parent widgets/badges if needed
        $this->dispatch('instruction-acknowledged');
    }

    public function render()
    {
        return view('livewire.instruction-acknowledge');
    }
}
