<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the authentication system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Authentication System...');
        
        // Check if users exist
        $userCount = User::count();
        $this->info("Total users in database: {$userCount}");
        
        if ($userCount > 0) {
            $users = User::all(['id', 'name', 'email', 'job_role']);
            $this->table(['ID', 'Name', 'Email', 'Job Role'], $users->toArray());
            
            // Test login with admin user
            $adminUser = User::where('email', 'admin@saliiyahotel.com')->first();
            if ($adminUser) {
                $this->info('Admin user found!');
                $this->info("Email: {$adminUser->email}");
                $this->info("Name: {$adminUser->name}");
                $this->info("Job Role: {$adminUser->job_role}");
                
                // Test password verification
                if (Hash::check('password123', $adminUser->password)) {
                    $this->info('✅ Password verification successful!');
                } else {
                    $this->error('❌ Password verification failed!');
                }
            } else {
                $this->error('❌ Admin user not found!');
            }
        } else {
            $this->error('❌ No users found in database!');
        }
        
        $this->info('Authentication test completed!');
    }
}
