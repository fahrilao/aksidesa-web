<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\RequestLegalLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RwApiTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $rwUser;
    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['is_active' => true]);
        
        $this->rwUser = User::factory()->create([
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);

        $this->operator = User::factory()->create([
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function rw_user_can_login_via_api()
    {
        $response = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password', // Default factory password
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'company',
                    ],
                    'token',
                    'token_type',
                ]
            ]);

        $this->assertEquals('RW', $response->json('data.user.role'));
        $this->assertEquals('Bearer', $response->json('data.token_type'));
    }

    /** @test */
    public function non_rw_user_cannot_login_via_api()
    {
        $response = $this->postJson('/api/rw/login', [
            'email' => $this->operator->email,
            'password' => 'password',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Only RW users can login via this API',
            ]);
    }

    /** @test */
    public function rw_user_can_create_request_after_login()
    {
        Storage::fake('public');

        // Login first
        $loginResponse = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        // Create fake images
        $ktpImage = UploadedFile::fake()->image('ktp.jpg');
        $kkImage = UploadedFile::fake()->image('kk.jpg');

        // Create request with file uploads
        $response = $this->post('/api/rw/requests', [
            'title' => 'Test Legal Letter Request',
            'description' => 'This is a test request via API',
            'ktp_image' => $ktpImage,
            'kk_image' => $kkImage,
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

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
            'title' => 'Test Legal Letter Request',
            'requested_by' => $this->rwUser->id,
            'status' => 'Waiting',
        ]);

        // Assert files were stored
        Storage::disk('public')->assertExists('documents/ktp/' . $ktpImage->hashName());
        Storage::disk('public')->assertExists('documents/kk/' . $kkImage->hashName());
    }

    /** @test */
    public function rw_user_can_view_their_requests()
    {
        // Create some requests
        RequestLegalLetter::factory()->count(2)->create([
            'requested_by' => $this->rwUser->id,
        ]);

        // Create request from different user (should not see this)
        $otherUser = User::factory()->create(['role' => 'RW']);
        RequestLegalLetter::factory()->create([
            'requested_by' => $otherUser->id,
        ]);

        // Login and get requests
        $loginResponse = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->getJson('/api/rw/requests');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertEquals(2, $response->json('data.total_count'));
    }

    /** @test */
    public function rw_user_can_filter_requests_by_status()
    {
        // Create requests with different statuses
        RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'status' => 'Pending',
        ]);
        RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'status' => 'Completed',
        ]);

        // Login
        $loginResponse = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        // Filter by status
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->getJson('/api/rw/requests?status=Pending');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.total_count'));
        $this->assertEquals('Pending', $response->json('data.status_filter'));
    }

    /** @test */
    public function rw_user_can_view_specific_request()
    {
        $request = RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
        ]);

        // Login
        $loginResponse = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->getJson("/api/rw/requests/{$request->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $request->id,
                    'title' => $request->title,
                ]
            ]);
    }

    /** @test */
    public function rw_user_cannot_view_other_users_requests()
    {
        $otherUser = User::factory()->create(['role' => 'RW']);
        $request = RequestLegalLetter::factory()->create([
            'requested_by' => $otherUser->id,
        ]);

        // Login
        $loginResponse = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->getJson("/api/rw/requests/{$request->id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Request not found or you do not have permission to view it',
            ]);
    }

    /** @test */
    public function rw_user_can_logout()
    {
        // Login first
        $loginResponse = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        // Logout
        $response = $this->postJson('/api/rw/logout', [], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);

        // Verify user has no active tokens after logout
        $this->rwUser->refresh();
        $this->assertEquals(0, $this->rwUser->tokens()->count());
    }

    /** @test */
    public function rw_user_can_get_profile()
    {
        // Login first
        $loginResponse = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->getJson('/api/rw/profile');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $this->rwUser->id,
                        'name' => $this->rwUser->name,
                        'email' => $this->rwUser->email,
                        'role' => 'RW',
                    ]
                ]
            ]);
    }

    /** @test */
    public function api_requires_authentication_for_protected_routes()
    {
        $response = $this->getJson('/api/rw/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function api_validates_request_creation_data()
    {
        // Login first
        $loginResponse = $this->postJson('/api/rw/login', [
            'email' => $this->rwUser->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        // Try to create request with invalid data (missing files)
        $response = $this->postJson('/api/rw/requests', [
            'title' => '', // Required field
            'description' => '', // Required field
            // Missing ktp_image and kk_image
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['title', 'description', 'ktp_image', 'kk_image']);
    }
}
