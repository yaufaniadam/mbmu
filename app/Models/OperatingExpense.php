<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatingExpense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'previous_version_id',
        'sppg_id',
        'name',
        'amount',
        'date',
        'category', // Deprecated, use category_id
        'category_id',
        'attachment',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }

    public function categoryData()
    {
        return $this->belongsTo(OperatingExpenseCategory::class, 'category_id');
    }

    // Relationship to see the OLD version
    public function previousVersion()
    {
        // We use paranoid() so we can retrieve the record even if it is soft-deleted
        return $this->belongsTo(OperatingExpense::class, 'previous_version_id')->withTrashed();
    }

    // Relationship to see the entire history (if you want to list them later)
    public function history()
    {
        return $this->hasMany(OperatingExpense::class, 'previous_version_id');
    }
}
