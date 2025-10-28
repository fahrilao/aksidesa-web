<?php

namespace Database\Seeders;

use App\Models\RequestLegalLetter;
use App\Models\User;
use App\Models\LegalLetter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RequestLegalLetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for assignment
        $operators = User::where('role', 'Operator')->get();
        $rwUsers = User::where('role', 'RW')->get();
        $legalLetters = LegalLetter::all();

        if ($rwUsers->isEmpty()) {
            $this->command->error('No RW users found. Please run UserSeeder first.');
            return;
        }

        // Create sample document images (placeholder paths)
        $this->createSampleDocuments();

        // Sample request data with Indonesian names and NIK
        $sampleData = [
            ['name' => 'Ahmad Suryanto', 'nik' => '3201012345678901', 'title' => 'Surat Keterangan Domisili', 'description' => 'Surat keterangan domisili untuk keperluan administrasi bank.'],
            ['name' => 'Siti Nurhaliza', 'nik' => '3201012345678902', 'title' => 'Surat Keterangan Tidak Mampu', 'description' => 'Permohonan surat keterangan tidak mampu untuk beasiswa.'],
            ['name' => 'Budi Santoso', 'nik' => '3201012345678903', 'title' => 'Surat Pengantar Nikah', 'description' => 'Surat pengantar untuk keperluan pernikahan di KUA.'],
            ['name' => 'Dewi Lestari', 'nik' => '3201012345678904', 'title' => 'Surat Keterangan Usaha', 'description' => 'Surat keterangan usaha untuk pengajuan kredit modal usaha.'],
            ['name' => 'Rudi Hartono', 'nik' => '3201012345678905', 'title' => 'Surat Keterangan Penghasilan', 'description' => 'Surat keterangan penghasilan untuk administrasi kredit rumah.'],
            ['name' => 'Maya Sari', 'nik' => '3201012345678906', 'title' => 'Surat Keterangan Belum Menikah', 'description' => 'Surat keterangan belum menikah untuk administrasi CPNS.'],
        ];

        foreach ($sampleData as $index => $data) {
            $rwUser = $rwUsers->random();
            $status = $rwUser->company_id ? ['Processing', 'Completed'][array_rand(['Processing', 'Completed'])] : 'Pending';
            
            RequestLegalLetter::create([
                'title' => $data['title'],
                'name' => $data['name'],
                'nik' => $data['nik'],
                'description' => $data['description'],
                'status' => $status,
                'requested_by' => $rwUser->id,
                'assigned_company_id' => $rwUser->company_id,
                'legal_letter_id' => ($status === 'Completed' && $legalLetters->isNotEmpty()) ? $legalLetters->random()->id : null,
                'ktp_image_path' => 'documents/ktp/sample_ktp_' . ($index + 1) . '.jpg',
                'kk_image_path' => 'documents/kk/sample_kk_' . ($index + 1) . '.jpg',
            ]);
        }

        $this->command->info('RequestLegalLetter seeder completed successfully.');
        $this->command->info('Created ' . count($sampleData) . ' sample request legal letters.');
    }

    /**
     * Create sample document directories and placeholder files
     */
    private function createSampleDocuments(): void
    {
        // Create directories if they don't exist
        Storage::disk('public')->makeDirectory('documents/ktp');
        Storage::disk('public')->makeDirectory('documents/kk');

        // Create placeholder text files (in real scenario, these would be actual images)
        for ($i = 1; $i <= 6; $i++) {
            $ktpContent = "Sample KTP document #{$i}\nThis is a placeholder for KTP image file.\nIn production, this would be an actual image file.";
            $kkContent = "Sample KK document #{$i}\nThis is a placeholder for KK image file.\nIn production, this would be an actual image file.";
            
            Storage::disk('public')->put("documents/ktp/sample_ktp_{$i}.jpg", $ktpContent);
            Storage::disk('public')->put("documents/kk/sample_kk_{$i}.jpg", $kkContent);
        }

        $this->command->info('Sample document files created in storage/app/public/documents/');
    }
}
