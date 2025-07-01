<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminExists = DB::table('users')->where('email', 'admin2@gmail.com')->first();

        if (!$adminExists)
        {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin2@gmail.com',
                'mobile' => '1234567890',
                'password' => Hash::make('admin123'),
                'utype' => 'ADM',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Admin user created successfully.');
            $this->command->info('Email: admin2@gmail.com');
            $this->command->info('Password: admin123');
            $this->command->warn('Please change the password after logging in for the first time.');
        } else {
            $this->command->info('Admin user already exists. No changes made.');
        }
    }
}