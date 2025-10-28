<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\RequestLegalLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $rwUser;
    protected $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['is_active' => true]);
        $this->apiKey = $this->company->generateApiKey();
        
        $this->rwUser = User::factory()->create([
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function company_can_access_api_with_valid_api_key()
    {
        // Create some request letters for the company
        RequestLegalLetter::factory()->count(3)->create([
            'requested_by' => $this->rwUser->id,
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->getJson('/api/company/requests');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'company' => [
                        'id',
                        'name',
                        'code',
                    ],
                    'requests',
                    'total_count',
                ]
            ]);

        $this->assertEquals(3, $response->json('data.total_count'));
    }

    /** @test */
    public function company_can_filter_requests_by_status()
    {
        // Create requests with different statuses
        RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'status' => 'Pending',
        ]);
        RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'status' => 'Processing',
        ]);
        RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'status' => 'Completed',
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->getJson('/api/company/requests?status=Pending');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.total_count'));
        $this->assertEquals('Pending', $response->json('data.status_filter'));
    }

    /** @test */
    public function company_can_get_statistics()
    {
        // Create requests with different statuses
        RequestLegalLetter::factory()->count(2)->create([
            'requested_by' => $this->rwUser->id,
            'status' => 'Pending',
        ]);
        RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
            'status' => 'Completed',
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->getJson('/api/company/requests/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'statistics' => [
                        'total' => 3,
                        'pending' => 2,
                        'processing' => 0,
                        'completed' => 1,
                    ]
                ]
            ]);
    }

    /** @test */
    public function company_can_view_specific_request()
    {
        $request = RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->getJson("/api/company/requests/{$request->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'request' => [
                        'id' => $request->id,
                        'title' => $request->title,
                    ]
                ]
            ]);
    }

    /** @test */
    public function api_requires_valid_api_key()
    {
        $response = $this->getJson('/api/company/requests');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'API key is required. Provide it via X-API-Key header or api_key query parameter.',
            ]);
    }

    /** @test */
    public function api_rejects_invalid_api_key()
    {
        $response = $this->withHeaders([
            'X-API-Key' => 'invalid-api-key'
        ])->getJson('/api/company/requests');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid API key or company is inactive.',
            ]);
    }

    /** @test */
    public function api_key_can_be_provided_via_query_parameter()
    {
        RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
        ]);

        $response = $this->getJson("/api/company/requests?api_key={$this->apiKey}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function api_updates_last_used_timestamp()
    {
        $originalLastUsed = $this->company->api_key_last_used_at;

        $this->getJson('/api/company/requests', [
            'X-API-Key' => $this->apiKey
        ]);

        $this->company->refresh();
        $this->assertNotEquals($originalLastUsed, $this->company->api_key_last_used_at);
        $this->assertNotNull($this->company->api_key_last_used_at);
    }

    /** @test */
    public function company_only_sees_requests_from_their_users()
    {
        // Create request from company user
        RequestLegalLetter::factory()->create([
            'requested_by' => $this->rwUser->id,
        ]);

        // Create request from different company user
        $otherCompany = Company::factory()->create(['is_active' => true]);
        $otherUser = User::factory()->create([
            'role' => 'RW',
            'company_id' => $otherCompany->id,
        ]);
        RequestLegalLetter::factory()->create([
            'requested_by' => $otherUser->id,
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->getJson('/api/company/requests');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.total_count'));
    }
}
