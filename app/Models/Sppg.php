<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sppg extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'sppg';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Ini PENTING untuk fungsi save() di Livewire Anda.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_sppg',
        'kode_sppg',
        'nama_bank',
        'nomor_va',
        'alamat',
        'kepala_sppg_id',
        'lembaga_pengusul_id',
        'province_code',
        'city_code',
        'district_code',
        'village_code',
    ];

    /**
     * --- INI ADALAH RELASI YANG HILANG (FIX) ---
     * Mendapatkan data User yang menjadi Kepala SPPG.
     */
    public function kepalaSppg(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kepala_sppg_id');
    }

    /**
     * --- INI RELASI KEDUA YANG DIPANGGIL ---
     * Mendapatkan data Lembaga Pengusul SPPG.
     */
    public function lembagaPengusul(): BelongsTo
    {
        return $this->belongsTo(LembagaPengusul::class, 'lembaga_pengusul_id');
    }

    /**
     * Mendapatkan semua user yang bertugas di SPPG ini (via pivot table).
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sppg_user_role')
            ->withPivot('role_id')
            ->withTimestamps();
    }
}
