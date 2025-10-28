<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyModelTest extends TestCase
{
    use RefreshDatabase;

    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Test Company',
            'code' => 'TEST',
            'address' => 'Test Address',
            'phone' => '123456789',
            'email' => 'test@company.com',
            'website' => 'https://test.com',
            'description' => 'Test Description',
            'is_active' => true,
        ]);
    }

    public function test_fillable_attributes()
    {
        $fillable = (new Company())->getFillable();
        $expectedFillable = [
            'name',
            'code',
            'address',
            'phone',
            'email',
            'website',
            'description',
            'is_active',
        ];
        
        $this->assertEquals($expectedFillable, $fillable);
    }

    public function test_casts_attributes()
    {
        $casts = (new Company())->getCasts();
        
        $this->assertArrayHasKey('is_active', $casts);
        $this->assertEquals('boolean', $casts['is_active']);
    }

    public function test_users_relationship()
    {
        // Create users for the company
        $operator = User::create([
            'name' => 'Test Operator',
            'email' => 'operator@test.com',
            'password' => bcrypt('password'),
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);

        $rwUser = User::create([
            'name' => 'Test RW',
            'email' => 'rw@test.com',
            'password' => bcrypt('password'),
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);

        $users = $this->company->users;
        
        $this->assertCount(2, $users);
        $this->assertTrue($users->contains($operator));
        $this->assertTrue($users->contains($rwUser));
    }

    public function test_active_scope()
    {
        // Create inactive company
        Company::create([
            'name' => 'Inactive Company',
            'code' => 'INACTIVE',
            'is_active' => false,
        ]);

        $activeCompanies = Company::active()->get();
        
        $this->assertCount(1, $activeCompanies);
        $this->assertEquals($this->company->id, $activeCompanies->first()->id);
    }

    public function test_operators_relationship()
    {
        // Create different types of users
        $operator1 = User::create([
            'name' => 'Operator 1',
            'email' => 'operator1@test.com',
            'password' => bcrypt('password'),
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);

        $operator2 = User::create([
            'name' => 'Operator 2',
            'email' => 'operator2@test.com',
            'password' => bcrypt('password'),
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);

        $rwUser = User::create([
            'name' => 'RW User',
            'email' => 'rw@test.com',
            'password' => bcrypt('password'),
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);

        $operators = $this->company->operators;
        
        $this->assertCount(2, $operators);
        $this->assertTrue($operators->contains($operator1));
        $this->assertTrue($operators->contains($operator2));
        $this->assertFalse($operators->contains($rwUser));
    }

    public function test_rw_users_relationship()
    {
        // Create different types of users
        $operator = User::create([
            'name' => 'Operator',
            'email' => 'operator@test.com',
            'password' => bcrypt('password'),
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);

        $rwUser1 = User::create([
            'name' => 'RW User 1',
            'email' => 'rw1@test.com',
            'password' => bcrypt('password'),
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);

        $rwUser2 = User::create([
            'name' => 'RW User 2',
            'email' => 'rw2@test.com',
            'password' => bcrypt('password'),
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);

        $rwUsers = $this->company->rwUsers;
        
        $this->assertCount(2, $rwUsers);
        $this->assertTrue($rwUsers->contains($rwUser1));
        $this->assertTrue($rwUsers->contains($rwUser2));
        $this->assertFalse($rwUsers->contains($operator));
    }

    public function test_company_creation_with_all_fields()
    {
        $companyData = [
            'name' => 'Full Company',
            'code' => 'FULL',
            'address' => 'Full Address',
            'phone' => '987654321',
            'email' => 'full@company.com',
            'website' => 'https://fullcompany.com',
            'description' => 'Full company description',
            'is_active' => true,
        ];

        $company = Company::create($companyData);

        $this->assertDatabaseHas('companies', $companyData);
        $this->assertEquals('Full Company', $company->name);
        $this->assertEquals('FULL', $company->code);
        $this->assertTrue($company->is_active);
    }

    public function test_company_creation_with_minimal_fields()
    {
        $companyData = [
            'name' => 'Minimal Company',
            'code' => 'MIN',
        ];

        $company = Company::create($companyData);

        $this->assertDatabaseHas('companies', [
            'name' => 'Minimal Company',
            'code' => 'MIN',
        ]);
        
        // Refresh from database to get default values
        $company = $company->fresh();
        
        $this->assertEquals('Minimal Company', $company->name);
        $this->assertEquals('MIN', $company->code);
        $this->assertTrue($company->is_active); // Default value
        $this->assertNull($company->address);
        $this->assertNull($company->phone);
        $this->assertNull($company->email);
        $this->assertNull($company->website);
        $this->assertNull($company->description);
    }
}
