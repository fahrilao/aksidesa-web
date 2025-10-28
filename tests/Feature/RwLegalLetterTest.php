<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\LegalLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class RwLegalLetterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $rwUser;
    private Company $company;
    private LegalLetter $legalLetter;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company
        $this->company = Company::factory()->create([
            'name' => 'Test Company',
            'code' => 'TEST001',
        ]);

        // Create an RW user associated with the company
        $this->rwUser = User::factory()->create([
            'name' => 'RW Test User',
            'email' => 'rw@test.com',
            'role' => 'RW',
            'company_id' => $this->company->id,
        ]);

        // Create a legal letter
        $this->legalLetter = LegalLetter::factory()->create([
            'title' => 'Test Legal Letter',
            'description' => 'This is a test legal letter description',
        ]);

        // Associate the legal letter with the company
        $this->legalLetter->companies()->attach($this->company->id, [
            'status' => 'active',
            'notes' => 'Test notes',
            'activated_at' => now(),
        ]);
    }

    public function test_rw_user_can_get_legal_letters()
    {
        Sanctum::actingAs($this->rwUser);

        $response = $this->getJson('/api/rw/legal-letters');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'legal_letters' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'created_by',
                            'created_at',
                            'updated_at',
                            'creator' => [
                                'id',
                                'name',
                                'email'
                            ],
                            'companies' => [
                                '*' => [
                                    'id',
                                    'name',
                                    'code',
                                    'pivot' => [
                                        'status',
                                        'notes',
                                        'activated_at'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                        'from',
                        'to'
                    ],
                    'company' => [
                        'id',
                        'name',
                        'code'
                    ],
                    'filters'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'company' => [
                        'id' => $this->company->id,
                        'name' => $this->company->name,
                        'code' => $this->company->code,
                    ]
                ]
            ]);

        // Check that the legal letter is in the response
        $this->assertCount(1, $response->json('data.legal_letters'));
        $this->assertEquals($this->legalLetter->id, $response->json('data.legal_letters.0.id'));
    }

    public function test_rw_user_can_get_legal_letters_with_status_filter()
    {
        Sanctum::actingAs($this->rwUser);

        $response = $this->getJson('/api/rw/legal-letters?status=active');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'filters' => [
                        'status' => 'active'
                    ]
                ]
            ]);

        $this->assertCount(1, $response->json('data.legal_letters'));
    }

    public function test_rw_user_can_get_legal_letters_with_pagination()
    {
        Sanctum::actingAs($this->rwUser);

        $response = $this->getJson('/api/rw/legal-letters?per_page=5&page=1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => 5
                    ]
                ]
            ]);
    }

    public function test_rw_user_can_get_specific_legal_letter()
    {
        Sanctum::actingAs($this->rwUser);

        $response = $this->getJson("/api/rw/legal-letters/{$this->legalLetter->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'legal_letter' => [
                        'id',
                        'title',
                        'description',
                        'created_by',
                        'created_at',
                        'updated_at',
                        'creator' => [
                            'id',
                            'name',
                            'email'
                        ],
                        'companies'
                    ],
                    'company' => [
                        'id',
                        'name',
                        'code'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'legal_letter' => [
                        'id' => $this->legalLetter->id,
                        'title' => $this->legalLetter->title,
                    ],
                    'company' => [
                        'id' => $this->company->id,
                        'name' => $this->company->name,
                        'code' => $this->company->code,
                    ]
                ]
            ]);
    }

    public function test_non_rw_user_cannot_access_legal_letters()
    {
        $operatorUser = User::factory()->create([
            'role' => 'Operator',
            'company_id' => $this->company->id,
        ]);

        Sanctum::actingAs($operatorUser);

        $response = $this->getJson('/api/rw/legal-letters');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Only RW users can access legal letters'
            ]);
    }

    public function test_rw_user_without_company_cannot_access_legal_letters()
    {
        $rwUserWithoutCompany = User::factory()->create([
            'role' => 'RW',
            'company_id' => null,
        ]);

        Sanctum::actingAs($rwUserWithoutCompany);

        $response = $this->getJson('/api/rw/legal-letters');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'User must be associated with a company to access legal letters'
            ]);
    }

    public function test_rw_user_cannot_access_legal_letter_from_different_company()
    {
        // Create another company and legal letter
        $otherCompany = Company::factory()->create();
        $otherLegalLetter = LegalLetter::factory()->create();
        $otherLegalLetter->companies()->attach($otherCompany->id, [
            'status' => 'active',
            'activated_at' => now(),
        ]);

        Sanctum::actingAs($this->rwUser);

        $response = $this->getJson("/api/rw/legal-letters/{$otherLegalLetter->id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Legal letter not found or you do not have permission to view it'
            ]);
    }

    public function test_invalid_status_filter_returns_error()
    {
        Sanctum::actingAs($this->rwUser);

        $response = $this->getJson('/api/rw/legal-letters?status=invalid');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid status. Valid statuses are: active, inactive'
            ]);
    }

    public function test_unauthenticated_user_cannot_access_legal_letters()
    {
        $response = $this->getJson('/api/rw/legal-letters');

        $response->assertStatus(401);
    }
}
