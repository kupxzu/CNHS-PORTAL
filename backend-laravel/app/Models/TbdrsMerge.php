<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbdrsMerge extends Model
{
    use HasFactory;

    protected $table = 'tbdrs_merge';

    protected $fillable = [
        'track_id',
        'building_id',
        'department_id',
        'section_id'
    ];

    /**
     * Get the track associated with this merge
     */
    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Get the building associated with this merge
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the department associated with this merge
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the section associated with this merge
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the student TBDRS merges associated with this TBDRS merge
     */
    public function studentTbdrsMerges()
    {
        return $this->hasMany(StdTbdrsMerge::class, 'tbdrs_id');
    }
}