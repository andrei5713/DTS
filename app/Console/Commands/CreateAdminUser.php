<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name= : Admin name} {--email= : Admin email} {--username= : Admin username} {--password= : Admin password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the first admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name') ?: $this->ask('Enter admin name');
        $email = $this->option('email') ?: $this->ask('Enter admin email');
        $username = $this->option('username') ?: $this->ask('Enter admin username');
        $password = $this->option('password') ?: $this->secret('Enter admin password');

        if (User::where('email', $email)->exists()) {
            $this->error('User with this email already exists!');
            return 1;
        }

        if (User::where('username', $username)->exists()) {
            $this->error('User with this username already exists!');
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info("Admin user '{$user->name}' created successfully!");
        $this->info("Email: {$user->email}");
        $this->info("Username: {$user->username}");
        $this->info("Role: {$user->role}");

        return 0;
    }
}
