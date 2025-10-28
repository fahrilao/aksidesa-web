<?php

namespace Tests\Unit;

use App\Models\LegalLetter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalLetterModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'title',
            'description',
            'created_by',
        ];

        $this->assertEquals($fillable, (new LegalLetter())->getFillable());
    }

    /** @test */
    public function it_has_timestamps()
    {
        $legalRequest = LegalLetter::factory()->create();

        $this->assertNotNull($legalRequest->created_at);
        $this->assertNotNull($legalRequest->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $legalRequest->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $legalRequest->updated_at);
    }

    /** @test */
    public function it_belongs_to_creator()
    {
        $creator = User::factory()->create();
        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $creator->id,
        ]);

        $this->assertInstanceOf(User::class, $legalRequest->creator);
        $this->assertEquals($creator->id, $legalRequest->creator->id);
    }




    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $creator = User::factory()->create();

        $data = [
            'title' => 'Test Legal Letter',
            'description' => 'Test description',
            'created_by' => $creator->id,
        ];

        $legalRequest = LegalLetter::create($data);

        $this->assertInstanceOf(LegalLetter::class, $legalRequest);
        $this->assertEquals('Test Legal Letter', $legalRequest->title);
        $this->assertEquals('Test description', $legalRequest->description);
        $this->assertEquals($creator->id, $legalRequest->created_by);
    }

    /** @test */
    public function it_can_be_updated()
    {
        $legalRequest = LegalLetter::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
        ]);

        $legalRequest->update([
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);

        $this->assertEquals('Updated Title', $legalRequest->fresh()->title);
        $this->assertEquals('Updated Description', $legalRequest->fresh()->description);
    }

    /** @test */
    public function it_has_many_to_many_relationship_with_companies()
    {
        $legalRequest = LegalLetter::factory()->create();
        $company1 = \App\Models\Company::factory()->create();
        $company2 = \App\Models\Company::factory()->create();

        $legalRequest->companies()->attach($company1->id, [
            'status' => 'active',
            'notes' => 'Test company 1',
            'activated_at' => now(),
        ]);

        $legalRequest->companies()->attach($company2->id, [
            'status' => 'inactive',
            'notes' => 'Test company 2',
            'deactivated_at' => now(),
        ]);

        $this->assertCount(2, $legalRequest->companies);
        $this->assertTrue($legalRequest->companies->contains($company1));
        $this->assertTrue($legalRequest->companies->contains($company2));
    }

    /** @test */
    public function it_can_get_active_companies()
    {
        $legalRequest = LegalLetter::factory()->create();
        $activeCompany = \App\Models\Company::factory()->create();
        $inactiveCompany = \App\Models\Company::factory()->create();

        $legalRequest->companies()->attach($activeCompany->id, [
            'status' => 'active',
            'activated_at' => now(),
        ]);

        $legalRequest->companies()->attach($inactiveCompany->id, [
            'status' => 'inactive',
            'deactivated_at' => now(),
        ]);

        $activeCompanies = $legalRequest->activeCompanies()->get();

        $this->assertCount(1, $activeCompanies);
        $this->assertTrue($activeCompanies->contains($activeCompany));
        $this->assertFalse($activeCompanies->contains($inactiveCompany));
    }

    /** @test */
    public function it_can_get_inactive_companies()
    {
        $legalRequest = LegalLetter::factory()->create();
        $activeCompany = \App\Models\Company::factory()->create();
        $inactiveCompany = \App\Models\Company::factory()->create();

        $legalRequest->companies()->attach($activeCompany->id, [
            'status' => 'active',
            'activated_at' => now(),
        ]);

        $legalRequest->companies()->attach($inactiveCompany->id, [
            'status' => 'inactive',
            'deactivated_at' => now(),
        ]);

        $inactiveCompanies = $legalRequest->inactiveCompanies()->get();

        $this->assertCount(1, $inactiveCompanies);
        $this->assertTrue($inactiveCompanies->contains($inactiveCompany));
        $this->assertFalse($inactiveCompanies->contains($activeCompany));
    }

    /** @test */
    public function it_can_attach_company_with_status()
    {
        $legalRequest = LegalLetter::factory()->create();
        $company = \App\Models\Company::factory()->create();
        $user = User::factory()->create();

        $legalRequest->attachCompany($company->id, 'active', 'Test attachment', $user->id);

        $this->assertDatabaseHas('legal_letter_company', [
            'request_legal_letter_id' => $legalRequest->id,
            'company_id' => $company->id,
            'status' => 'active',
            'notes' => 'Test attachment',
            'updated_by' => $user->id,
        ]);

        $attachedCompany = $legalRequest->companies()->first();
        $this->assertEquals('active', $attachedCompany->pivot->status);
        $this->assertEquals('Test attachment', $attachedCompany->pivot->notes);
        $this->assertNotNull($attachedCompany->pivot->activated_at);
    }

    /** @test */
    public function it_can_update_company_status()
    {
        $legalRequest = LegalLetter::factory()->create();
        $company = \App\Models\Company::factory()->create();
        $user = User::factory()->create();

        // First attach the company
        $legalRequest->attachCompany($company->id, 'active', 'Initial status', $user->id);

        // Then update the status
        $legalRequest->updateCompanyStatus($company->id, 'inactive', 'Updated status', $user->id);

        $this->assertDatabaseHas('legal_letter_company', [
            'request_legal_letter_id' => $legalRequest->id,
            'company_id' => $company->id,
            'status' => 'inactive',
            'notes' => 'Updated status',
            'updated_by' => $user->id,
        ]);

        $attachedCompany = $legalRequest->companies()->first();
        $this->assertEquals('inactive', $attachedCompany->pivot->status);
        $this->assertEquals('Updated status', $attachedCompany->pivot->notes);
        $this->assertNotNull($attachedCompany->pivot->deactivated_at);
    }
}
