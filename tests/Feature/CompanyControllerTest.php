<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
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
        $this->company = Company::create([
            'name' => 'Test Company',
            'code' => 'TEST',
            'address' => 'Test Address',
            'phone' => '123456789',
            'email' => 'test@company.com',
            'website' => 'https://test.com',
            'description' => 'Test company description',
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

    public function test_administrator_can_view_companies_index()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson('/companies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'code',
                            'address',
                            'phone',
                            'email',
                            'website',
                            'description',
                            'is_active'
                        ]
                    ]
                ]
            ]);
    }

    public function test_operator_can_view_companies_index()
    {
        $response = $this->actingAs($this->operator)
            ->getJson('/companies');

        $response->assertStatus(200);
    }

    public function test_rw_user_cannot_view_companies_index()
    {
        $response = $this->actingAs($this->rwUser)
            ->getJson('/companies');

        $response->assertStatus(403);
    }

    public function test_administrator_can_create_company()
    {
        $companyData = [
            'name' => 'New Company',
            'code' => 'NEWCO',
            'address' => 'New Address',
            'phone' => '987654321',
            'email' => 'new@company.com',
            'website' => 'https://newcompany.com',
            'description' => 'New company description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->administrator)
            ->postJson('/companies', $companyData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'code',
                    'address',
                    'phone',
                    'email',
                    'website',
                    'description',
                    'is_active'
                ]
            ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'New Company',
            'code' => 'NEWCO',
            'email' => 'new@company.com',
        ]);
    }

    public function test_company_code_must_be_unique()
    {
        $companyData = [
            'name' => 'Another Company',
            'code' => 'TEST', // Same as existing company
            'address' => 'Another Address',
            'phone' => '987654321',
            'email' => 'another@company.com',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->administrator)
            ->postJson('/companies', $companyData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_administrator_can_view_company()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson("/companies/{$this->company->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'code',
                    'address',
                    'phone',
                    'email',
                    'website',
                    'description',
                    'is_active'
                ]
            ]);
    }

    public function test_administrator_can_update_company()
    {
        $updateData = [
            'name' => 'Updated Company Name',
            'address' => 'Updated Address',
            'phone' => '999888777',
        ];

        $response = $this->actingAs($this->administrator)
            ->putJson("/companies/{$this->company->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company updated successfully'
            ]);

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => 'Updated Company Name',
            'address' => 'Updated Address',
            'phone' => '999888777',
        ]);
    }

    public function test_administrator_can_delete_empty_company()
    {
        // Create a company without users
        $emptyCompany = Company::create([
            'name' => 'Empty Company',
            'code' => 'EMPTY',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->administrator)
            ->deleteJson("/companies/{$emptyCompany->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company deleted successfully'
            ]);

        $this->assertDatabaseMissing('companies', [
            'id' => $emptyCompany->id,
        ]);
    }

    public function test_cannot_delete_company_with_users()
    {
        $response = $this->actingAs($this->administrator)
            ->deleteJson("/companies/{$this->company->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot delete company that has users assigned to it'
            ]);

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
        ]);
    }

    public function test_administrator_can_toggle_company_status()
    {
        $originalStatus = $this->company->is_active;

        $response = $this->actingAs($this->administrator)
            ->putJson("/companies/{$this->company->id}/toggle-status");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company status updated successfully'
            ]);

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'is_active' => !$originalStatus,
        ]);
    }

    public function test_administrator_can_get_company_users()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson("/companies/{$this->company->id}/users");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'role'
                    ]
                ]
            ]);
    }

    public function test_get_active_companies()
    {
        $response = $this->actingAs($this->operator)
            ->getJson('/companies/active/list');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'code'
                    ]
                ]
            ]);
    }

    public function test_operator_cannot_create_company()
    {
        $companyData = [
            'name' => 'New Company',
            'code' => 'NEWCO',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->operator)
            ->postJson('/companies', $companyData);

        $response->assertStatus(403);
    }

    public function test_operator_cannot_update_company()
    {
        $updateData = [
            'name' => 'Updated Name',
        ];

        $response = $this->actingAs($this->operator)
            ->putJson("/companies/{$this->company->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_operator_cannot_delete_company()
    {
        $response = $this->actingAs($this->operator)
            ->deleteJson("/companies/{$this->company->id}");

        $response->assertStatus(403);
    }

    public function test_search_companies_by_name()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson('/companies?search=Test');

        $response->assertStatus(200);
        
        $companies = $response->json('data.data');
        $this->assertNotEmpty($companies);
        $this->assertStringContainsString('Test', $companies[0]['name']);
    }

    public function test_filter_companies_by_active_status()
    {
        // Create inactive company
        Company::create([
            'name' => 'Inactive Company',
            'code' => 'INACTIVE',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->administrator)
            ->getJson('/companies?active=true');

        $response->assertStatus(200);
        
        $companies = $response->json('data.data');
        foreach ($companies as $company) {
            $this->assertTrue($company['is_active']);
        }
    }

    public function test_companies_with_user_counts()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson('/companies?with_users=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'code',
                            'users_count',
                            'operators_count',
                            'rw_users_count'
                        ]
                    ]
                ]
            ]);
    }
}
