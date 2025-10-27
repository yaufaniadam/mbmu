<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LembagaPengusul extends Model
{
    use HasFactory;
    protected $table = 'lembaga_pengusul';
    protected $guarded = ['id'];

    public function sppgs()
    {
        return $this->hasMany(Sppg::class);
    }

    /**
     * Mendapatkan data user Pimpinan dari lembaga ini.
     */
    public function pimpinan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pimpinan_id');
    }
}