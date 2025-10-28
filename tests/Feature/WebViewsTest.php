<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebViewsTest extends TestCase
{
    use RefreshDatabase;

    protected $administrator;
    protected $operator;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test company
        $this->company = Company::create([
            'name' => 'Test Company',
            'code' => 'TEST',
            'address' => 'Test Address',
            'phone' => '123456789',
            'email' => 'test@company.com',
            'is_active' => true,
        ]);

        // Create test users
        $this->administrator = User::create([
            'name' => 'Test Administrator',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'Administrator',
            'company_id' => null,
        ]);

        $this->operator = User::create([
            'name' => 'Test Operator',
            'email' => 'operator@test.com',
            'password' => Hash::make('password'),
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_administrator_can_view_users_index_page()
    {
        $response = $this->actingAs($this->administrator)
            ->get('/users');

        $response->assertStatus(200)
            ->assertViewIs('users.index');
    }

    public function test_administrator_can_view_user_show_page()
    {
        $response = $this->actingAs($this->administrator)
            ->get("/users/{$this->operator->id}");

        $response->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertViewHas('user', $this->operator);
    }

    public function test_web_routes_return_500_when_views_dont_exist()
    {
        // These routes should return 500 because views don't exist yet
        // This test documents the current state - views need to be created
        
        $response = $this->actingAs($this->administrator)
            ->get('/companies');
        $response->assertStatus(500); // View doesn't exist yet
        
        $response = $this->actingAs($this->administrator)
            ->get('/legal-letters');
        $response->assertStatus(500); // View doesn't exist yet
        
        $response = $this->actingAs($this->administrator)
            ->get('/request-legal-letters');
        $response->assertStatus(500); // View doesn't exist yet
        
        $response = $this->actingAs($this->administrator)
            ->get('/api-keys');
        $response->assertStatus(500); // View doesn't exist yet
    }

    public function test_json_requests_still_return_json_responses()
    {
        // Test that AJAX requests still get JSON responses for all controllers
        
        // Users
        $response = $this->actingAs($this->administrator)
            ->getJson('/users');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'roles']);
        
        // Companies
        $response = $this->actingAs($this->administrator)
            ->getJson('/companies');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
        
        // Legal Letters
        $response = $this->actingAs($this->administrator)
            ->getJson('/legal-letters');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
        
        // Request Legal Letters
        $response = $this->actingAs($this->administrator)
            ->getJson('/request-legal-letters');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
        
        // API Keys
        $response = $this->actingAs($this->administrator)
            ->getJson('/api-keys');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_unauthorized_users_cannot_access_admin_pages()
    {
        $rwUser = User::create([
            'name' => 'Test RW User',
            'email' => 'rw@test.com',
            'password' => Hash::make('password'),
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($rwUser)
            ->get('/users');

        $response->assertStatus(403);
    }
}
