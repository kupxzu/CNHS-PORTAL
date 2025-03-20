<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_name',
        'room_number',
        'building_id'
    ];

    /**
     * Get the building that owns the room
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}