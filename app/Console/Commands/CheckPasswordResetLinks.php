<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPasswordResetLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:check-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for password reset links in logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for password reset links in logs...');
        
        // Read the latest log file
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            $this->error('No log file found.');
            return;
        }
        
        $logContent = file_get_contents($logFile);
        
        // Look for password reset link entries
        if (strpos($logContent, 'Password Reset Link Generated') !== false) {
            $this->info('Found password reset links in logs:');
            $this->line('');
            
            // Extract and display the reset links
            preg_match_all('/Password Reset Link Generated.*?reset_url.*?:\s*(https?:\/\/[^\s]+)/s', $logContent, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $index => $url) {
                    $this->line(($index + 1) . '. ' . $url);
                }
            }
        } else {
            $this->warn('No password reset links found in logs.');
            $this->line('Try requesting a password reset first.');
        }
    }
}
