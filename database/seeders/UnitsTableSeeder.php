<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::firstOrCreate([
            'code' => 'CPMSD',
        ], [
            'name' => 'Corporate Planning and Management Services Department',
        ]);
    }
}
