<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Sppg extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'sppg';

    protected $casts = [
        'province_code' => 'string',
        'city_code' => 'string',
        'district_code' => 'string',
        'village_code' => 'string',
    ];

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
        'balance',
        'alamat',
        'is_active',
        'tanggal_mulai_sewa',
        'kepala_sppg_id',
        'lembaga_pengusul_id',
        'province_code',
        'city_code',
        'district_code',
        'village_code',
        'latitude',
        'longitude',
        'photo_path',
        'grade',
        'izin_operasional_path',
        'sertifikat_halal_path',
        'slhs_path',
        'lhaccp_path',
        'iso_path',
        'sertifikat_lahan_path',
        'dokumen_lain_path',
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

    public function kepalaPengusul(): HasOneThrough
    {
        // Join lembaga_pengusul.id = sppg.lembaga_pengusul_id
        // then users.id = lembaga_pengusul.pimpinan_id
        return $this->hasOneThrough(
            User::class,
            LembagaPengusul::class,
            'id', // first key on through (lembaga_pengusul.id)
            'id', // key on related (users.id)
            'lembaga_pengusul_id', // local key on this model (sppg.lembaga_pengusul_id)
            'pimpinan_id' // local key on through (lembaga_pengusul.pimpinan_id)
        );
    }

    /**
     * Mendapatkan semua user yang bertugas di SPPG ini (via pivot table).
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sppg_user_roles')
            ->withPivot(['role_id', 'sk_path'])
            ->withTimestamps()
            ->distinct();
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function productionSchedules()
    {
        return $this->hasMany(ProductionSchedule::class);
    }

    public function staffs()
    {
        return $this->hasMany(SppgUserRole::class);
    }

    public function verificationSetting()
    {
        return $this->hasOne(ProductionVerificationSetting::class);
    }

    public function distributions()
    {
        return $this->hasManyThrough(
            Distribution::class,
            ProductionSchedule::class,
            'sppg_id',             // Foreign key on 'jadwal_produksi' table
            'jadwal_produksi_id',  // Foreign key on 'distribusi' table
            'id',                  // Local key on 'sppg' table
            'id'                   // Local key on 'jadwal_produksi' table
        );
    }

    public function latestBill()
    {
        return $this->hasOne(Bill::class)->latestOfMany('period_end');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function operatingExpenses()
    {
        return $this->hasMany(OperatingExpense::class);
    }

    public function operatingExpenseCategories()
    {
        return $this->hasMany(OperatingExpenseCategory::class);
    }

    public function incomingFunds()
    {
        return $this->hasMany(SppgIncomingFund::class);
    }

    public function volunteers()
    {
        return $this->hasMany(Volunteer::class);
    }

    public function volunteerAttendances()
    {
        return $this->hasMany(VolunteerAttendance::class);
    }
}
