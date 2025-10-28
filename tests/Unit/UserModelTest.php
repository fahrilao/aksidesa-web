<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $administrator;
    protected $operator;
    protected $rwUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Test Company',
            'code' => 'TEST',
            'is_active' => true,
        ]);

        $this->administrator = User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'Administrator',
            'company_id' => null,
        ]);

        $this->operator = User::create([
            'name' => 'Operator',
            'email' => 'operator@test.com',
            'password' => bcrypt('password'),
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);

        $this->rwUser = User::create([
            'name' => 'RW User',
            'email' => 'rw@test.com',
            'password' => bcrypt('password'),
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_is_administrator_method()
    {
        $this->assertTrue($this->administrator->isAdministrator());
        $this->assertFalse($this->operator->isAdministrator());
        $this->assertFalse($this->rwUser->isAdministrator());
    }

    public function test_is_operator_method()
    {
        $this->assertFalse($this->administrator->isOperator());
        $this->assertTrue($this->operator->isOperator());
        $this->assertFalse($this->rwUser->isOperator());
    }

    public function test_is_rw_method()
    {
        $this->assertFalse($this->administrator->isRW());
        $this->assertFalse($this->operator->isRW());
        $this->assertTrue($this->rwUser->isRW());
    }

    public function test_get_roles_method()
    {
        $roles = User::getRoles();
        $expectedRoles = ['Administrator', 'Operator', 'RW'];
        
        $this->assertEquals($expectedRoles, $roles);
    }

    public function test_has_permission_level_method()
    {
        // Administrator should have all permission levels
        $this->assertTrue($this->administrator->hasPermissionLevel('RW'));
        $this->assertTrue($this->administrator->hasPermissionLevel('Operator'));
        $this->assertTrue($this->administrator->hasPermissionLevel('Administrator'));

        // Operator should have Operator and RW permissions
        $this->assertTrue($this->operator->hasPermissionLevel('RW'));
        $this->assertTrue($this->operator->hasPermissionLevel('Operator'));
        $this->assertFalse($this->operator->hasPermissionLevel('Administrator'));

        // RW should only have RW permissions
        $this->assertTrue($this->rwUser->hasPermissionLevel('RW'));
        $this->assertFalse($this->rwUser->hasPermissionLevel('Operator'));
        $this->assertFalse($this->rwUser->hasPermissionLevel('Administrator'));
    }

    public function test_requires_company_method()
    {
        $this->assertFalse($this->administrator->requiresCompany());
        $this->assertTrue($this->operator->requiresCompany());
        $this->assertTrue($this->rwUser->requiresCompany());
    }

    public function test_company_relationship()
    {
        // Administrator should not have company
        $this->assertNull($this->administrator->company);

        // Operator and RW should have company
        $this->assertInstanceOf(Company::class, $this->operator->company);
        $this->assertInstanceOf(Company::class, $this->rwUser->company);
        
        $this->assertEquals($this->company->id, $this->operator->company->id);
        $this->assertEquals($this->company->id, $this->rwUser->company->id);
    }

    public function test_fillable_attributes()
    {
        $fillable = (new User())->getFillable();
        $expectedFillable = ['name', 'email', 'password', 'role', 'company_id'];
        
        $this->assertEquals($expectedFillable, $fillable);
    }

    public function test_hidden_attributes()
    {
        $hidden = (new User())->getHidden();
        $expectedHidden = ['password', 'remember_token'];
        
        $this->assertEquals($expectedHidden, $hidden);
    }

    public function test_casts_attributes()
    {
        $casts = (new User())->getCasts();
        
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
    }
}
