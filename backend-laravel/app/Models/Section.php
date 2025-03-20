<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_name'
    ];

    /**
     * Get the TBDRS merges that include this section
     */
    public function tbdrsMerges()
    {
        return $this->hasMany(TbdrsMerge::class);
    }
}