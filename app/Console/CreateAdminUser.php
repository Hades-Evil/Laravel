<?php

namespace App\Console;

use Illuminate\Console;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=} {--password=} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for the NextGen Store';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating a new admin user...');
        
        // Get input
        $name = $this->option('name') ?? $this->ask('Enter admin name', 'NextGen Admin');
        $email = $this->option('email') ?? $this->ask('Enter admin email', 'admin@nextgenstore.com');
        $password = $this->option('password') ?? $this->secret('Enter admin password');
        
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        
        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }
        
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'mobile' => '0000000000', // Default mobile number
                'password' => Hash::make($password),
                'utype' => 'ADM',
                'email_verified_at' => now(),
            ]);
            
            $this->info('Admin user created successfully!');
            $this->info('Name: ' . $user->name);
            $this->info('Email: ' . $user->email);
            $this->info('User Type: ' . $user->utype);
            $this->warn('Please keep the credentials secure!');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return 1;
        }
    }
}
