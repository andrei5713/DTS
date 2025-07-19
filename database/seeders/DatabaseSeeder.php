<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $cpmsd = Unit::create([
            'code' => 'CPMSD',
            'name' => 'Corporate Planning and Management Services Department',
        ]);

        User::create([
            'name' => 'CPMSD Department',
            'username' => 'CPMSD',
            'email' => 'cpmsd@nfa.gov.ph',
            'password' => Hash::make('Default123@password'),
            'unit_id' => $cpmsd->id,
            'role' => 'department',
        ]);

        $this->call([
            UnitsTableSeeder::class,
            UserSeeder::class,
        ]);
    }
}
