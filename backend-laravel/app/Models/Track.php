<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_name'
    ];

    /**
     * Get the TBDRS merges that include this track
     */
    public function tbdrsMerges()
    {
        return $this->hasMany(TbdrsMerge::class);
    }
}