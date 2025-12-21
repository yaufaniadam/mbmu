<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SppgIncomingFund extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'previous_version_id',
        'sppg_id',
        'user_id',
        'amount',
        'source', // Deprecated? Or keep as specific detail?
        'category_id',
        'received_at',
        'notes',
        'attachment',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }
    
    public function category() // Relationship
    {
        return $this->belongsTo(SppgIncomingFundCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to see the OLD version
    public function previousVersion()
    {
        // We use paranoid() so we can retrieve the record even if it is soft-deleted
        return $this->belongsTo(SppgIncomingFund::class, 'previous_version_id')->withTrashed();
    }

    // Relationship to see the entire history (if you want to list them later)
    public function history()
    {
        return $this->hasMany(SppgIncomingFund::class, 'previous_version_id');
    }
}
