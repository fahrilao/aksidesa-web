<?php

namespace Tests\Feature;

use App\Models\RequestLegalLetter;
use App\Models\LegalLetter;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RequestLegalLetterWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $administrator;
    protected $operator;
    protected $rwUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company first
        $this->company = Company::factory()->create();

        // Create users
        $this->administrator = User::factory()->create([
            'role' => 'Administrator',
            'company_id' => null,
        ]);

        $this->operator = User::factory()->create([
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);

        $this->rwUser = User::factory()->create([
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function rw_user_can_create_request()
    {
        Storage::fake('public');
        $this->actingAs($this->rwUser);

        $ktpImage = UploadedFile::fake()->image('ktp.jpg');
        $kkImage = UploadedFile::fake()->image('kk.jpg');

        $requestData = [
            'title' => 'Need Legal Letter for Contract',
            'name' => 'John Doe',
            'nik' => '1234567890123456',
            'description' => 'I need a legal letter to handle contract dispute with vendor.',
            'ktp_image' => $ktpImage,
            'kk_image' => $kkImage,
        ];

        $response = $this->postJson('/request-legal-letters', $requestData);

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
                    'status',
                    'requested_by',
                    'requester',
                ]
            ]);

        $this->assertDatabaseHas('request_legal_letters', [
            'title' => 'Need Legal Letter for Contract',
            'requested_by' => $this->rwUser->id,
            'name' => 'John Doe',
            'nik' => '1234567890123456',
        ]);
    }

    /** @test */
    public function operator_can_assign_request_to_self()
    {
        $this->actingAs($this->operator);

        // Create a waiting request
        $request = RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'status' => 'Waiting',
        ]);

        $response = $this->postJson("/request-legal-letters/{$request->id}/assign");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Request assigned successfully',
            ]);

        $this->assertDatabaseHas('request_legal_letters', [
            'id' => $request->id,
            'assigned_company_id' => $this->operator->company_id,
            'status' => 'Processing',
        ]);
    }

    /** @test */
    public function operator_can_update_request_status()
    {
        $this->actingAs($this->operator);

        // Create a request assigned to the operator
        $request = RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'assigned_company_id' => $this->operator->company_id,
            'status' => 'Processing',
        ]);

        $response = $this->putJson("/request-legal-letters/{$request->id}/status", [
            'status' => 'Completed'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Request status updated successfully',
            ]);

        $this->assertDatabaseHas('request_legal_letters', [
            'id' => $request->id,
            'status' => 'Completed',
        ]);
    }

    /** @test */
    public function operator_can_complete_request_and_create_legal_letter()
    {
        $this->actingAs($this->operator);

        // Create a request assigned to the operator
        $request = RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'assigned_company_id' => $this->operator->company_id,
            'status' => 'Processing',
        ]);

        $response = $this->postJson("/request-legal-letters/{$request->id}/complete");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Request completed and legal letter created successfully',
            ]);

        // Check that request is completed
        $this->assertDatabaseHas('request_legal_letters', [
            'id' => $request->id,
            'status' => 'Completed',
        ]);

        // Check that legal letter was created
        $request->refresh();
        $this->assertNotNull($request->legal_letter_id);
        
        $this->assertDatabaseHas('legal_letters', [
            'id' => $request->legal_letter_id,
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => $this->operator->id,
        ]);
    }

    /** @test */
    public function users_can_view_their_relevant_requests()
    {
        // RW user can see their own requests
        $this->actingAs($this->rwUser);

        $rwRequest = RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
        ]);

        $otherRequest = RequestLegalLetter::factory()->create([
            'requested_by' => $this->operator->id,
        ]);

        $response = $this->getJson('/request-legal-letters');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data'); // Only sees their own request

        // Operator can see assigned requests and unassigned ones
        $this->actingAs($this->operator);

        $response = $this->getJson('/request-legal-letters');
        $response->assertStatus(200);
        // Should see unassigned requests and requests assigned to them
    }

    /** @test */
    public function non_rw_users_cannot_create_requests()
    {
        $this->actingAs($this->operator);

        $requestData = [
            'title' => 'Test Request',
            'description' => 'Test Description',
        ];

        $response = $this->postJson('/request-legal-letters', $requestData);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Only RW users can create legal letter requests',
            ]);
    }
}
