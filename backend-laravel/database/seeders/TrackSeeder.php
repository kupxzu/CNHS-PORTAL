<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Track;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracks = [
            'Academic',
            'TVL',
            'Arts and Design',
            'Sports Track'
        ];

        foreach ($tracks as $trackName) {
            Track::firstOrCreate(['track_name' => $trackName]);
        }
    }
}