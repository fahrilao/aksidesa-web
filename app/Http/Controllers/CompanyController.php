<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies
     */
    public function index(Request $request)
    {
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $query = Company::query();
            
            // Filter by active status
            if ($request->has('active')) {
                $isActive = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
                $query->where('is_active', $isActive);
            }
            
            // Search by name or code
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }
            
            // Include user counts if requested
            if ($request->has('with_users')) {
                $query->withCount(['users', 'operators', 'rwUsers']);
            }
            
            $companies = $query->orderBy('name')->paginate(15);
            
            return response()->json([
                'success' => true,
                'data' => $companies
            ]);
        }

        // For web requests, return the view
        return view('companies.index');
    }

    /**
     * Store a newly created company
     */
    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 201);
    }

    /**
     * Show the form for creating a new company
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Display the specified company
     */
    public function show(Company $company, Request $request)
    {
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            // Load users if requested
            if ($request->has('with_users')) {
                $company->load(['users' => function($query) {
                    $query->select('id', 'name', 'email', 'role', 'company_id');
                }]);
            }

            return response()->json([
                'success' => true,
                'data' => $company
            ]);
        }

        // For web requests, return the view
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified company
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully',
            'data' => $company->fresh()
        ]);
    }

    /**
     * Remove the specified company
     */
    public function destroy(Company $company)
    {
        // Check if company has users
        if ($company->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete company that has users assigned to it'
            ], 422);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully'
        ]);
    }

    /**
     * Get active companies for dropdown/select
     */
    public function getActiveCompanies()
    {
        $companies = Company::active()
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $companies
        ]);
    }

    /**
     * Toggle company active status
     */
    public function toggleStatus(Company $company)
    {
        $company->update(['is_active' => !$company->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Company status updated successfully',
            'data' => $company->fresh()
        ]);
    }

    /**
     * Get company users
     */
    public function getUsers(Company $company, Request $request)
    {
        $query = $company->users();
        
        // Filter by role if provided
        if ($request->has('role') && in_array($request->role, ['Operator', 'RW'])) {
            $query->where('role', $request->role);
        }
        
        $users = $query->select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Generate API key for company
     */
    public function generateApiKey(Company $company)
    {
        try {
            $apiKey = $company->generateApiKey();
            
            return response()->json([
                'success' => true,
                'message' => 'API key generated successfully',
                'data' => [
                    'api_key' => $apiKey,
                    'created_at' => $company->fresh()->api_key_created_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate API key'
            ], 500);
        }
    }

    /**
     * Revoke API key for company
     */
    public function revokeApiKey(Company $company)
    {
        try {
            $company->revokeApiKey();
            
            return response()->json([
                'success' => true,
                'message' => 'API key revoked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke API key'
            ], 500);
        }
    }
}
