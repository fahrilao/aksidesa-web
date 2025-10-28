<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $administrator;
    protected $operator;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['is_active' => true]);
        
        $this->administrator = User::factory()->create([
            'role' => 'Administrator',
            'company_id' => null,
        ]);

        $this->operator = User::factory()->create([
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function administrator_can_generate_api_key_for_company()
    {
        $this->actingAs($this->administrator);

        $response = $this->postJson("/companies/{$this->company->id}/api-key/generate");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'API key generated successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'company_id',
                    'company_name',
                    'api_key',
                    'created_at',
                ]
            ]);

        $this->company->refresh();
        $this->assertNotNull($this->company->api_key);
        $this->assertStringStartsWith('ck_', $this->company->api_key);
    }

    /** @test */
    public function non_administrator_cannot_generate_api_key()
    {
        $this->actingAs($this->operator);

        $response = $this->postJson("/companies/{$this->company->id}/api-key/generate");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient permissions',
            ]);
    }

    /** @test */
    public function administrator_can_view_api_key_status()
    {
        $this->actingAs($this->administrator);

        // Generate API key first
        $this->company->generateApiKey();

        $response = $this->getJson("/companies/{$this->company->id}/api-key");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'company_id' => $this->company->id,
                    'company_name' => $this->company->name,
                    'has_api_key' => true,
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'api_key_created_at',
                    'api_key_last_used_at',
                ]
            ]);

        // Should not expose the actual API key
        $response->assertJsonMissing(['api_key']);
    }

    /** @test */
    public function administrator_can_regenerate_api_key()
    {
        $this->actingAs($this->administrator);

        // Generate initial API key
        $oldApiKey = $this->company->generateApiKey();

        $response = $this->postJson("/companies/{$this->company->id}/api-key/regenerate");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'API key regenerated successfully',
            ]);

        $this->company->refresh();
        $this->assertNotEquals($oldApiKey, $this->company->api_key);
        $this->assertStringStartsWith('ck_', $this->company->api_key);
    }

    /** @test */
    public function administrator_can_revoke_api_key()
    {
        $this->actingAs($this->administrator);

        // Generate API key first
        $this->company->generateApiKey();

        $response = $this->deleteJson("/companies/{$this->company->id}/api-key");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'API key revoked successfully',
            ]);

        $this->company->refresh();
        $this->assertNull($this->company->api_key);
    }

    /** @test */
    public function administrator_can_view_all_companies_api_key_status()
    {
        $this->actingAs($this->administrator);

        // Create another company with API key
        $company2 = Company::factory()->create(['is_active' => true]);
        $company2->generateApiKey();

        $response = $this->getJson('/api-keys');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'code',
                        'is_active',
                        'has_api_key',
                        'api_key_created_at',
                        'api_key_last_used_at',
                    ]
                ]
            ]);
    }
}
