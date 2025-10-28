<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\LegalLetter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LegalLetterControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $administrator;
    protected $operator;
    protected $rwUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test company
        $this->company = Company::factory()->create([
            'name' => 'Test Company',
            'is_active' => true,
        ]);

        // Create test users
        $this->administrator = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'Administrator',
            'company_id' => null,
        ]);

        $this->operator = User::factory()->create([
            'name' => 'Operator User',
            'email' => 'operator@test.com',
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);

        $this->rwUser = User::factory()->create([
            'name' => 'RW User',
            'email' => 'rw@test.com',
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function administrator_can_create_legal_letter_request()
    {
        $this->actingAs($this->administrator);

        $requestData = [
            'title' => 'Test Legal Letter',
            'description' => 'Test description for legal letter',
        ];

        $response = $this->postJson('/legal-letters', $requestData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Legal letter request created successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'created_by',
                    'creator',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('legal_letters', [
            'title' => 'Test Legal Letter',
            'created_by' => $this->administrator->id,
        ]);
    }

    /** @test */
    public function non_administrator_cannot_create_legal_letter_request()
    {
        $this->actingAs($this->operator);

        $requestData = [
            'title' => 'Test Legal Letter',
            'description' => 'Test description',
        ];

        $response = $this->postJson('/legal-letters', $requestData);

        $response->assertStatus(403);
    }

    /** @test */
    public function authenticated_users_can_view_legal_letter_requests()
    {
        $this->actingAs($this->operator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        $response = $this->getJson('/legal-letters');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'creator',
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function authenticated_users_can_view_single_legal_letter_request()
    {
        $this->actingAs($this->operator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        $response = $this->getJson("/legal-letters/{$legalRequest->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $legalRequest->id,
                    'title' => $legalRequest->title,
                ]
            ]);
    }

    /** @test */
    public function administrator_can_update_any_legal_letter_request()
    {
        $this->actingAs($this->administrator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->operator->id,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ];

        $response = $this->putJson("/legal-letters/{$legalRequest->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Legal letter request updated successfully',
                'data' => [
                    'title' => 'Updated Title',
                    'description' => 'Updated Description',
                ]
            ]);

        $this->assertDatabaseHas('legal_letters', [
            'id' => $legalRequest->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);
    }

    /** @test */
    public function user_can_update_request_they_created()
    {
        $this->actingAs($this->operator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->operator->id,
        ]);

        $updateData = [
            'description' => 'Updated description by creator',
        ];

        $response = $this->putJson("/legal-letters/{$legalRequest->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'description' => 'Updated description by creator',
                ]
            ]);
    }

    /** @test */
    public function user_cannot_update_request_they_did_not_create()
    {
        $this->actingAs($this->rwUser);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        $updateData = [
            'title' => 'Updated Title',
        ];

        $response = $this->putJson("/legal-letters/{$legalRequest->id}", $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function administrator_can_delete_legal_letter_request()
    {
        $this->actingAs($this->administrator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        $response = $this->deleteJson("/legal-letters/{$legalRequest->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Legal letter request deleted successfully',
            ]);

        $this->assertDatabaseMissing('legal_letters', [
            'id' => $legalRequest->id,
        ]);
    }

    /** @test */
    public function non_administrator_cannot_delete_legal_letter_request()
    {
        $this->actingAs($this->operator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->operator->id,
        ]);

        $response = $this->deleteJson("/legal-letters/{$legalRequest->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function can_get_statistics()
    {
        $this->actingAs($this->operator);

        LegalLetter::factory()->create(['created_by' => $this->administrator->id]);
        LegalLetter::factory()->create(['created_by' => $this->operator->id]);
        LegalLetter::factory()->create(['created_by' => $this->administrator->id]);

        $response = $this->getJson('/legal-letters-statistics');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'total',
                    'by_creator',
                ]
            ]);

        $data = $response->json('data');
        $this->assertEquals(3, $data['total']);
    }


    /** @test */
    public function validation_fails_for_invalid_data()
    {
        $this->actingAs($this->administrator);

        $invalidData = [
            'title' => '', // Required field
            'description' => '', // Required field
        ];

        $response = $this->postJson('/legal-letters', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'description',
            ]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_endpoints()
    {
        $response = $this->getJson('/legal-letters');
        $response->assertStatus(401);

        $response = $this->postJson('/legal-letters', []);
        $response->assertStatus(401);
    }

    /** @test */
    public function administrator_can_attach_companies_to_legal_request()
    {
        $this->actingAs($this->administrator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $response = $this->postJson("/legal-letters/{$legalRequest->id}/companies", [
            'company_ids' => [$company1->id, $company2->id],
            'status' => 'active',
            'notes' => 'Initial attachment',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Companies attached successfully',
            ]);

        $this->assertDatabaseHas('legal_letter_company', [
            'request_legal_letter_id' => $legalRequest->id,
            'company_id' => $company1->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('legal_letter_company', [
            'request_legal_letter_id' => $legalRequest->id,
            'company_id' => $company2->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function operator_can_toggle_status_for_their_company()
    {
        $this->actingAs($this->operator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        // Attach the operator's company to the request
        $legalRequest->attachCompany($this->company->id, 'active', 'Initial status', $this->administrator->id);

        $response = $this->putJson("/legal-letters/{$legalRequest->id}/companies/{$this->company->id}/status", [
            'status' => 'inactive',
            'notes' => 'Temporarily disabled',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company status updated to inactive',
            ]);

        $this->assertDatabaseHas('legal_letter_company', [
            'request_legal_letter_id' => $legalRequest->id,
            'company_id' => $this->company->id,
            'status' => 'inactive',
            'notes' => 'Temporarily disabled',
        ]);
    }

    /** @test */
    public function operator_cannot_toggle_status_for_other_company()
    {
        $this->actingAs($this->operator);

        $otherCompany = Company::factory()->create();
        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        // Attach the other company to the request
        $legalRequest->attachCompany($otherCompany->id, 'active', 'Initial status', $this->administrator->id);

        $response = $this->putJson("/legal-letters/{$legalRequest->id}/companies/{$otherCompany->id}/status", [
            'status' => 'inactive',
            'notes' => 'Should not work',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You can only manage status for your own company',
            ]);
    }

    /** @test */
    public function can_get_companies_for_legal_request()
    {
        $this->actingAs($this->operator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $legalRequest->attachCompany($company1->id, 'active', 'Active company', $this->administrator->id);
        $legalRequest->attachCompany($company2->id, 'inactive', 'Inactive company', $this->administrator->id);

        $response = $this->getJson("/legal-letters/{$legalRequest->id}/companies");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');

        $companies = $response->json('data');
        $this->assertEquals('active', $companies[0]['pivot']['status']);
        $this->assertEquals('inactive', $companies[1]['pivot']['status']);
    }

    /** @test */
    public function operator_can_get_legal_requests_for_their_company()
    {
        $this->actingAs($this->operator);

        $legalRequest1 = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);
        $legalRequest2 = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        $legalRequest1->attachCompany($this->company->id, 'active', 'Active request', $this->administrator->id);
        $legalRequest2->attachCompany($this->company->id, 'inactive', 'Inactive request', $this->administrator->id);

        $response = $this->getJson("/companies/{$this->company->id}/legal-letters");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'company',
                    'requests' => [
                        '*' => [
                            'id',
                            'title',
                            'pivot' => [
                                'status',
                                'notes',
                            ]
                        ]
                    ]
                ]
            ]);

        $requests = $response->json('data.requests');
        $this->assertCount(2, $requests);
    }

    /** @test */
    public function operator_cannot_get_legal_requests_for_other_company()
    {
        $this->actingAs($this->operator);

        $otherCompany = Company::factory()->create();

        $response = $this->getJson("/companies/{$otherCompany->id}/legal-letters");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You can only view requests for your own company',
            ]);
    }

    /** @test */
    public function administrator_can_detach_company_from_legal_request()
    {
        $this->actingAs($this->administrator);

        $legalRequest = LegalLetter::factory()->create([
            'created_by' => $this->administrator->id,
        ]);

        $company = Company::factory()->create();
        $legalRequest->attachCompany($company->id, 'active', 'To be removed', $this->administrator->id);

        $response = $this->deleteJson("/legal-letters/{$legalRequest->id}/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company detached successfully',
            ]);

        $this->assertDatabaseMissing('legal_letter_company', [
            'request_legal_letter_id' => $legalRequest->id,
            'company_id' => $company->id,
        ]);
    }
}
