<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Director\'s Office',
                'code' => 'DO',
                'full_name' => 'CPMSD/DO',
                'description' => 'Director\'s Office - Main administrative unit',
                'is_active' => true,
            ],
            [
                'name' => 'Corporate Planning Division',
                'code' => 'CPD',
                'full_name' => 'CPMSD/CPD',
                'description' => 'Corporate Planning Division - Handles corporate planning and development',
                'is_active' => true,
            ],
            [
                'name' => 'Information and Communication Technology Services Division',
                'code' => 'ICTSD',
                'full_name' => 'CPMSD/ICTSD',
                'description' => 'Information and Communication Technology Services Division - IT and communication services',
                'is_active' => true,
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
