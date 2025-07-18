<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the CPMSD unit from database
        $cpmsd = Unit::where('code', 'CPMSD')->first();

        if (!$cpmsd) {
            // Prevent seeding if unit doesn't exist
            throw new \Exception('CPMSD unit not found. Make sure Units are seeded first.');
        }

        User::where('username', 'cpmsd')->delete();

        // Create department user safely using existing unit ID
        User::create([
            'name' => 'CPMSD Department',
            'username' => 'CPMSD',
            'email' => 'cpmsd@nfa.gov.ph',
            'password' => Hash::make('Default123@password'),
            'unit_id' => $cpmsd->id,
            'role' => 'department',
        ]);
    }
}
