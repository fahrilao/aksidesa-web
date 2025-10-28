<?php

namespace Database\Seeders;

use App\Models\LegalLetter;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LegalLetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for assignment
        $administrator = User::where('role', 'Administrator')->first();
        $operators = User::where('role', 'Operator')->get();
        $rwUsers = User::where('role', 'RW')->get();

        if (!$administrator) {
            $this->command->error('No Administrator found. Please run UserSeeder first.');
            return;
        }

        // Sample legal letter requests
        $requests = [
            [
                'title' => 'Contract Breach Warning Letter',
                'description' => 'Warning letter for breach of service contract terms and conditions. Client has failed to meet payment obligations for the past 3 months.',
                'created_by' => $administrator->id,
            ],
            [
                'title' => 'Debt Collection Demand Letter',
                'description' => 'Formal demand letter for outstanding debt collection. Total amount due: Rp 150,000,000.',
                'created_by' => $administrator->id,
            ],
            [
                'title' => 'Lease Termination Notice',
                'description' => 'Legal notice for lease termination due to violation of lease agreement terms.',
                'created_by' => $administrator->id,
            ],
            [
                'title' => 'Partnership Agreement Draft',
                'description' => 'Draft partnership agreement for new business venture in technology sector.',
                'created_by' => $administrator->id,
            ],
            [
                'title' => 'Employment Termination Letter',
                'description' => 'Legal letter for employee termination due to misconduct and policy violations.',
                'created_by' => $administrator->id,
            ],
        ];

        // Get all companies
        $companies = Company::all();

        foreach ($requests as $request) {
            $legalLetter = LegalLetter::create($request);
            
            // Attach all companies to each legal letter with active status
            foreach ($companies as $company) {
                $legalLetter->companies()->attach($company->id, [
                    'status' => 'active',
                    'notes' => 'Seeded legal letter available for all companies',
                    'activated_at' => now(),
                    'updated_by' => $administrator->id,
                ]);
            }
        }

        $this->command->info('LegalLetter seeder completed successfully.');
    }
}
