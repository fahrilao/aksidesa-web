<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
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

    public function test_administrator_can_view_users_index()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson('/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'role',
                            'company_id',
                            'company'
                        ]
                    ]
                ],
                'roles'
            ]);
    }

    public function test_operator_can_view_users_index()
    {
        $response = $this->actingAs($this->operator)
            ->getJson('/users');

        $response->assertStatus(200);
    }

    public function test_rw_user_cannot_view_users_index()
    {
        $response = $this->actingAs($this->rwUser)
            ->getJson('/users');

        $response->assertStatus(403);
    }

    public function test_administrator_can_create_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->administrator)
            ->postJson('/users', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'company_id'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@test.com',
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_administrator_cannot_be_assigned_to_company()
    {
        $userData = [
            'name' => 'New Admin',
            'email' => 'newadmin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Administrator',
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->administrator)
            ->postJson('/users', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['company_id']);
    }

    public function test_non_administrator_requires_company()
    {
        $userData = [
            'name' => 'New Operator',
            'email' => 'newoperator@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Operator',
            // Omit company_id entirely to test validation
        ];

        $response = $this->actingAs($this->administrator)
            ->postJson('/users', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['company_id']);
    }

    public function test_administrator_can_update_user()
    {
        $updateData = [
            'name' => 'Updated Name',
            'role' => 'RW',
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->administrator)
            ->putJson("/users/{$this->operator->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User updated successfully'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->operator->id,
            'name' => 'Updated Name',
            'role' => 'RW',
        ]);
    }

    public function test_administrator_can_delete_user()
    {
        $response = $this->actingAs($this->administrator)
            ->deleteJson("/users/{$this->operator->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $this->operator->id,
        ]);
    }

    public function test_cannot_delete_last_administrator()
    {
        $response = $this->actingAs($this->administrator)
            ->deleteJson("/users/{$this->administrator->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot delete the last Administrator'
            ]);
    }

    public function test_operator_cannot_create_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'RW',
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->operator)
            ->postJson('/users', $userData);

        $response->assertStatus(403);
    }

    public function test_administrator_can_update_user_role()
    {
        $roleData = [
            'role' => 'RW',
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->administrator)
            ->putJson("/users/{$this->operator->id}/role", $roleData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User role updated successfully'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->operator->id,
            'role' => 'RW',
        ]);
    }

    public function test_administrator_can_bulk_update_roles()
    {
        $bulkData = [
            'users' => [
                [
                    'id' => $this->operator->id,
                    'role' => 'RW',
                    'company_id' => $this->company->id,
                ],
                [
                    'id' => $this->rwUser->id,
                    'role' => 'Operator',
                    'company_id' => $this->company->id,
                ]
            ]
        ];

        $response = $this->actingAs($this->administrator)
            ->postJson('/users/bulk-roles', $bulkData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Bulk role update completed'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->operator->id,
            'role' => 'RW',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->rwUser->id,
            'role' => 'Operator',
        ]);
    }

    public function test_get_users_by_role()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson('/users/role/Operator');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'company'
                    ]
                ]
            ]);
    }

    public function test_get_users_by_invalid_role()
    {
        $response = $this->actingAs($this->administrator)
            ->getJson('/users/role/InvalidRole');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid role specified'
            ]);
    }
}
