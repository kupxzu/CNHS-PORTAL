<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_name'
    ];

    /**
     * Get the rooms associated with the building
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get the TBDRS merges that include this building
     */
    public function tbdrsMerges()
    {
        return $this->hasMany(TbdrsMerge::class);
    }
}