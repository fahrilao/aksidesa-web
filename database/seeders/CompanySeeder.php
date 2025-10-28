<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'PT. Teknologi Maju',
                'code' => 'TEKMA',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'phone' => '021-12345678',
                'email' => 'info@teknologimaju.com',
                'website' => 'https://teknologimaju.com',
                'description' => 'Desa teknologi terdepan di Indonesia',
                'is_active' => true,
            ],
            [
                'name' => 'CV. Solusi Digital',
                'code' => 'SOLDIG',
                'address' => 'Jl. Gatot Subroto No. 456, Bandung',
                'phone' => '022-87654321',
                'email' => 'contact@solusidigitak.co.id',
                'website' => 'https://solusidigitak.co.id',
                'description' => 'Penyedia solusi digital untuk bisnis modern',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Inovasi Kreatif',
                'code' => 'INOKREAT',
                'address' => 'Jl. Diponegoro No. 789, Surabaya',
                'phone' => '031-11223344',
                'email' => 'hello@inovasikreatif.com',
                'website' => 'https://inovasikreatif.com',
                'description' => 'Desa yang fokus pada inovasi dan kreativitas',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
