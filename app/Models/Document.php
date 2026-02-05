<?php

namespace App\Models;

use App\Enums\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'keterangan',
        'kategori',
        'file_path',
        'tanggal',
        'user_id',
        'lembaga_pengusul_id',
    ];

    protected $casts = [
        'kategori' => DocumentCategory::class,
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lembagaPengusul(): BelongsTo
    {
        return $this->belongsTo(LembagaPengusul::class);
    }
}
