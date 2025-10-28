<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CheckUserRoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $administrator;
    protected $operator;
    protected $rwUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test company
        $this->company = Company::create([
            'name' => 'Test Company',
            'code' => 'TEST',
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

        $this->rwUser = User::create([
            'name' => 'Test RW User',
            'email' => 'rw@test.com',
            'password' => Hash::make('password'),
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/users');
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_administrator_can_access_administrator_routes()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson('/users');
        
        $response->assertStatus(200);
    }

    public function test_administrator_can_access_operator_routes()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson('/companies');
        
        $response->assertStatus(200);
    }

    public function test_operator_can_access_operator_routes()
    {
        $response = $this->actingAs($this->operator)
            ->getJson('/users');
        
        $response->assertStatus(200);
    }

    public function test_operator_cannot_access_administrator_routes()
    {
        $companyData = [
            'name' => 'New Company',
            'code' => 'NEW',
        ];

        $response = $this->actingAs($this->operator)
            ->postJson('/companies', $companyData);
        
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient permissions'
            ]);
    }

    public function test_rw_user_cannot_access_operator_routes()
    {
        $response = $this->actingAs($this->rwUser)
            ->getJson('/users');
        
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient permissions'
            ]);
    }

    public function test_rw_user_cannot_access_administrator_routes()
    {
        $companyData = [
            'name' => 'New Company',
            'code' => 'NEW',
        ];

        $response = $this->actingAs($this->rwUser)
            ->postJson('/companies', $companyData);
        
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient permissions'
            ]);
    }

    public function test_permission_hierarchy_works_correctly()
    {
        // Administrator should access all levels
        $adminRoutes = [
            '/users',
            '/companies',
            '/companies/active/list'
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($this->administrator)->getJson($route);
            $this->assertTrue($response->status() === 200, "Administrator should access {$route}");
        }

        // Operator should access operator and above routes
        $operatorRoutes = [
            '/users',
            '/companies',
            '/companies/active/list'
        ];

        foreach ($operatorRoutes as $route) {
            $response = $this->actingAs($this->operator)->getJson($route);
            $this->assertTrue($response->status() === 200, "Operator should access {$route}");
        }

        // RW should not access operator or administrator routes
        $restrictedRoutes = [
            '/users',
            '/companies'
        ];

        foreach ($restrictedRoutes as $route) {
            $response = $this->actingAs($this->rwUser)->getJson($route);
            $this->assertTrue($response->status() === 403, "RW should not access {$route}");
        }
    }

    public function test_middleware_works_with_route_parameters()
    {
        // Administrator can access specific user
        $response = $this->actingAs($this->administrator)
            ->getJson("/users/{$this->operator->id}");
        $response->assertStatus(200);

        // Operator can access specific user (read-only)
        $response = $this->actingAs($this->operator)
            ->getJson("/users/{$this->administrator->id}");
        $response->assertStatus(200);

        // RW cannot access specific user
        $response = $this->actingAs($this->rwUser)
            ->getJson("/users/{$this->administrator->id}");
        $response->assertStatus(403);
    }

    public function test_middleware_works_with_different_http_methods()
    {
        // Administrator can POST (create)
        $userData = [
            'name' => 'New User',
            'email' => 'new@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'RW',
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->administrator)
            ->postJson('/users', $userData);
        $response->assertStatus(201);

        // Operator cannot POST (create)
        $userData2 = [
            'name' => 'Another User',
            'email' => 'another@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'RW',
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->operator)
            ->postJson('/users', $userData2);
        $response->assertStatus(403);
    }
}
