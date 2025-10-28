<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiKeyController extends Controller
{
    /**
     * Display API key information for a company
     */
    public function show(Request $request, Company $company)
    {
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'has_api_key' => $company->hasApiKey(),
                    'api_key_created_at' => $company->api_key_created_at,
                    'api_key_last_used_at' => $company->api_key_last_used_at,
                    // Don't expose the actual API key for security
                ]
            ]);
        }

        // For web requests, return the view
        return view('api-keys.show', compact('company'));
    }

    /**
     * Generate a new API key for a company
     */
    public function generate(Company $company): JsonResponse
    {
        // Only administrators can generate API keys
        if (!Auth::user()->isAdministrator()) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can generate API keys'
            ], 403);
        }

        $apiKey = $company->generateApiKey();

        return response()->json([
            'success' => true,
            'message' => 'API key generated successfully',
            'data' => [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'api_key' => $apiKey, // Only show on generation
                'created_at' => $company->api_key_created_at,
            ]
        ]);
    }

    /**
     * Regenerate API key for a company
     */
    public function regenerate(Company $company): JsonResponse
    {
        // Only administrators can regenerate API keys
        if (!Auth::user()->isAdministrator()) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can regenerate API keys'
            ], 403);
        }

        if (!$company->hasApiKey()) {
            return response()->json([
                'success' => false,
                'message' => 'Company does not have an existing API key'
            ], 400);
        }

        $apiKey = $company->generateApiKey();

        return response()->json([
            'success' => true,
            'message' => 'API key regenerated successfully',
            'data' => [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'api_key' => $apiKey, // Only show on regeneration
                'created_at' => $company->api_key_created_at,
            ]
        ]);
    }

    /**
     * Revoke API key for a company
     */
    public function revoke(Company $company): JsonResponse
    {
        // Only administrators can revoke API keys
        if (!Auth::user()->isAdministrator()) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can revoke API keys'
            ], 403);
        }

        if (!$company->hasApiKey()) {
            return response()->json([
                'success' => false,
                'message' => 'Company does not have an API key to revoke'
            ], 400);
        }

        $company->revokeApiKey();

        return response()->json([
            'success' => true,
            'message' => 'API key revoked successfully',
            'data' => [
                'company_id' => $company->id,
                'company_name' => $company->name,
            ]
        ]);
    }

    /**
     * List all companies with their API key status
     */
    public function index(Request $request)
    {
        // Only administrators can view all API keys
        if (!Auth::user()->isAdministrator()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only administrators can view API key information'
                ], 403);
            }
            
            abort(403, 'Only administrators can view API key information');
        }

        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $companies = Company::select([
                'id', 'name', 'code', 'is_active', 
                'api_key_created_at', 'api_key_last_used_at'
            ])
            ->selectRaw('CASE WHEN api_key IS NOT NULL THEN true ELSE false END as has_api_key')
            ->orderBy('name')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $companies
            ]);
        }

        // For web requests, return the view
        return view('api-keys.index');
    }
}
