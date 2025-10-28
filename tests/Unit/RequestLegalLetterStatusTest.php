<?php

namespace Tests\Unit;

use App\Models\RequestLegalLetter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestLegalLetterStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function request_legal_letter_has_waiting_as_default_status()
    {
        $user = User::factory()->create(['role' => 'RW']);
        
        $request = RequestLegalLetter::create([
            'title' => 'Test Request',
            'name' => 'Test User',
            'nik' => '1234567890123456',
            'description' => 'Test description',
            'requested_by' => $user->id,
        ]);

        $this->assertEquals('Waiting', $request->status);
    }

    /** @test */
    public function factory_can_create_waiting_status_requests()
    {
        $request = RequestLegalLetter::factory()->waiting()->create();
        
        $this->assertEquals('Waiting', $request->status);
    }

    /** @test */
    public function waiting_scope_filters_correctly()
    {
        // Create requests with different statuses (without company assignment to avoid FK issues)
        RequestLegalLetter::factory()->waiting()->create(['assigned_company_id' => null]);
        RequestLegalLetter::factory()->pending()->create(['assigned_company_id' => null]);
        RequestLegalLetter::factory()->create(['status' => 'Processing', 'assigned_company_id' => null]);
        
        $waitingRequests = RequestLegalLetter::waiting()->get();
        
        $this->assertCount(1, $waitingRequests);
        $this->assertEquals('Waiting', $waitingRequests->first()->status);
    }

    /** @test */
    public function all_status_scopes_work_correctly()
    {
        // Create one request of each status (without company assignment to avoid FK issues)
        RequestLegalLetter::factory()->waiting()->create(['assigned_company_id' => null]);
        RequestLegalLetter::factory()->pending()->create(['assigned_company_id' => null]);
        RequestLegalLetter::factory()->create(['status' => 'Processing', 'assigned_company_id' => null]);
        RequestLegalLetter::factory()->create(['status' => 'Completed', 'assigned_company_id' => null]);
        
        $this->assertCount(1, RequestLegalLetter::waiting()->get());
        $this->assertCount(1, RequestLegalLetter::pending()->get());
        $this->assertCount(1, RequestLegalLetter::processing()->get());
        $this->assertCount(1, RequestLegalLetter::completed()->get());
    }

    /** @test */
    public function status_enum_includes_all_expected_values()
    {
        $expectedStatuses = ['Waiting', 'Pending', 'Processing', 'Completed'];
        
        foreach ($expectedStatuses as $status) {
            $request = RequestLegalLetter::factory()->create(['status' => $status]);
            $this->assertEquals($status, $request->status);
        }
    }
}
