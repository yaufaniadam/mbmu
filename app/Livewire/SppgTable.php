<?php

namespace App\Livewire; // Namespace yang benar untuk Livewire 3

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sppg;
use App\Models\User;
use App\Models\LembagaPengusul;
use Livewire\Attributes\Url; // Menggunakan atribut URL Livewire 3

class SppgTable extends Component
{
    use WithPagination;

    // Properti untuk Modal, dengan atribut #[Url] untuk mencegah state di URL
    #[Url(except: true)]
    public $showModal = false;
    #[Url(except: true)]
    public $showDeleteModal = false;

    public $sppgId;
    public Sppg $sppg; // Properti untuk form binding (Livewire 3 style)

    // Properti untuk Sorting & Searching
    public $search = '';
    public $sortField = 'nama_sppg';
    public $sortDirection = 'asc';

    // Properti untuk data dropdown
    public $allUsers = [];
    public $allLembaga = [];

    /**
     * Aturan validasi.
     */
    protected function rules()
    {
        return [
            'sppg.nama_sppg' => 'required|string|max:255',
            'sppg.kode_sppg' => 'required|string|max:10|unique:sppg,kode_sppg,' . $this->sppgId,
            'sppg.alamat' => 'required|string',
            'sppg.kepala_sppg_id' => 'nullable|exists:users,id',
            'sppg.lembaga_pengusul_id' => 'nullable|exists:lembaga_pengusul,id',
        ];
    }

    /**
     * Dijalankan saat komponen di-load pertama kali.
     */
    public function mount()
    {
        // Ambil data untuk dropdown sekali saja
        $this->allUsers = User::orderBy('name')->get();
        $this->allLembaga = LembagaPengusul::orderBy('nama_lembaga')->get();
        $this->sppg = $this->makeBlankSppg();
    }

    /**
     * Membuat instance model SPPG kosong untuk form.
     */
    public function makeBlankSppg()
    {
        return Sppg::make(); // Menggunakan make() untuk model baru
    }

    // --- FUNGSI UTAMA (RENDER) ---

    public function render()
    {
        $sppgs = Sppg::with(['kepalaSppg', 'lembagaPengusul'])
            ->where('nama_sppg', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.sppg-table', [
            'sppgs' => $sppgs,
        ]);
    }

    // --- FUNGSI SORTING & SEARCHING ---

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    // Dipanggil setiap kali input search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- FUNGSI CRUD & MODAL ---

    /**
     * Menyiapkan modal untuk membuat data baru.
     */
    public function create()
    {
        $this->resetErrorBag();
        $this->sppgId = null;
        $this->sppg = $this->makeBlankSppg();
        $this->showModal = true;
    }

    /**
     * Menyiapkan modal untuk mengedit data yang ada.
     */
    public function edit(Sppg $sppg)
    {
        $this->resetErrorBag();
        $this->sppgId = $sppg->id;
        $this->sppg = $sppg; // Muat data SPPG yang ada
        $this->showModal = true;
    }

    /**
     * Menyimpan data (baik baru maupun update).
     */
    public function save()
    {
        // Validasi data
        $this->validate();

        // Simpan data
        $this->sppg->save();

        session()->flash('success', 'Data SPPG berhasil disimpan.');
        $this->showModal = false; // Ini akan ditangkap oleh @entangle
    }

    /**
     * Menampilkan modal konfirmasi hapus.
     */
    public function delete($id)
    {
        $this->sppgId = $id;
        $this->showDeleteModal = true; // Ini akan ditangkap oleh @entangle
    }

    /**
     * Mengeksekusi penghapusan data.
     */
    public function confirmDelete()
    {
        Sppg::find($this->sppgId)->delete();

        session()->flash('success', 'Data SPPG berhasil dihapus.');
        $this->showDeleteModal = false; // Ini akan ditangkap oleh @entangle
    }
}

