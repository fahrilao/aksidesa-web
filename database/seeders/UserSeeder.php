<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get companies for assignment
        $companies = Company::all();
        
        // Create Administrator (no company assignment)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'Administrator',
            'company_id' => null,
        ]);

        // Create Operators for each company
        foreach ($companies as $index => $company) {
            User::create([
                'name' => "Operator {$company->name}",
                'email' => "operator" . ($index + 1) . "@example.com",
                'password' => Hash::make('password'),
                'role' => 'Operator',
                'company_id' => $company->id,
            ]);
        }

        // Create RW users for each company
        foreach ($companies as $index => $company) {
            User::create([
                'name' => "RW User {$company->name}",
                'email' => "rw" . ($index + 1) . "@example.com",
                'password' => Hash::make('password'),
                'role' => 'RW',
                'company_id' => $company->id,
            ]);
            
            // Create additional RW user for first company
            if ($index === 0) {
                User::create([
                    'name' => "RW User 2 {$company->name}",
                    'email' => "rw4@example.com", // Changed to avoid conflict
                    'password' => Hash::make('password'),
                    'role' => 'RW',
                    'company_id' => $company->id,
                ]);
            }
        }
    }
}
