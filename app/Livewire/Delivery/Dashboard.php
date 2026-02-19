<?php

namespace App\Livewire\Delivery;

use App\Models\Distribution;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Layout('layouts.delivery')]
#[Title('Dashboard Pengantaran')]
class Dashboard extends Component
{
    use WithFileUploads;

    public $activeTab = 'delivery'; // 'delivery' or 'pickup'
    public $photo;
    public $confirmingId = null;
    public $notes = '';

    public function mount()
    {
        if (!Auth::check() || !Auth::user()->hasRole('Staf Pengantaran')) {
            return redirect()->route('delivery.login');
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->reset(['confirmingId', 'photo', 'notes']);
    }

    public function confirmAction($id)
    {
        $this->confirmingId = $id;
        $this->reset(['photo', 'notes']);
    }

    public function cancelAction()
    {
        $this->reset(['confirmingId', 'photo', 'notes']);
    }

    public function completeDelivery($id)
    {
        $this->validate([
            'notes' => 'nullable|string|max:255',
             // Photo is optional for now, can be made required if needed
            'photo' => 'nullable|image|max:10240', // 10MB max
        ]);

        $distribution = Distribution::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $path = null;
        if ($this->photo) {
            $path = $this->photo->store('delivery_proofs', 'public');
        }

        $distribution->update([
            'status_pengantaran' => 'Terkirim',
            'delivered_at' => now(),
            'photo_of_proof' => $path,
            'notes' => $this->notes,
        ]);

        $this->reset(['confirmingId', 'photo', 'notes']);
        session()->flash('message', 'Pengantaran berhasil diselesaikan!');
    }

    public function completePickup($id)
    {
        $this->validate([
            'notes' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:10240',
        ]);

        $distribution = Distribution::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $path = null;
        if ($this->photo) {
            $path = $this->photo->store('pickup_proofs', 'public');
        }

        $distribution->update([
            'pickup_status' => 'Selesai',
            'pickup_at' => now(),
            'pickup_photo_proof' => $path,
            'pickup_notes' => $this->notes,
        ]);

        $this->reset(['confirmingId', 'photo', 'notes']);
        session()->flash('message', 'Penjemputan berhasil diselesaikan!');
    }

    public function render()
    {
        $user = Auth::user();

        $deliveries = Distribution::with(['school', 'productionSchedule'])
            ->where('user_id', $user->id)
            ->where('status_pengantaran', '!=', 'Terkirim') // Show pending/in-progress
            ->orderBy('created_at', 'desc')
            ->get();

        $pickups = Distribution::with(['school', 'productionSchedule'])
            ->where('user_id', $user->id)
            ->where('status_pengantaran', 'Terkirim') // Must be delivered first
            ->where('pickup_status', '!=', 'Selesai') // Show pending pickups
            ->orderBy('delivered_at', 'desc')
            ->get();

        return view('livewire.delivery.dashboard', [
            'tasks' => $this->activeTab === 'delivery' ? $deliveries : $pickups,
        ]);
    }
}
