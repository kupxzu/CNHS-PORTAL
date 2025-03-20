<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StdTbdrsMerge extends Model
{
    use HasFactory;

    protected $table = 'std_tbdrs_merge';

    protected $fillable = [
        'uusers_id',
        'tbdrs_id'
    ];

    /**
     * Get the user associated with this merge
     */
    public function user()
    {
        return $this->belongsTo(UUser::class, 'uusers_id');
    }

    /**
     * Get the TBDRS merge associated with this student merge
     */
    public function tbdrsMerge()
    {
        return $this->belongsTo(TbdrsMerge::class, 'tbdrs_id');
    }
}