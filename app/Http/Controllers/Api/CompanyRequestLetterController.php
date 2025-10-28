<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequestLegalLetter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyRequestLetterController extends Controller
{
    /**
     * Get request letters by status for the authenticated company
     */
    public function getByStatus(Request $request): JsonResponse
    {
        $company = $request->get('api_company');
        $status = $request->query('status');

        // Validate status parameter
        $validStatuses = ['Pending', 'Processing', 'Completed'];
        if ($status && !in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status. Valid statuses are: ' . implode(', ', $validStatuses)
            ], 400);
        }

        // Get users from this company
        $companyUserIds = $company->users()->pluck('id');

        // Build query for request letters from company users
        $query = RequestLegalLetter::whereIn('requested_by', $companyUserIds)
            ->with(['requester:id,name,email', 'assignedCompany:id,name,code', 'legalLetter:id,title']);

        // Filter by status if provided
        if ($status) {
            $query->where('status', $status);
        }

        // Order by most recent first
        $requests = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'code' => $company->code,
                ],
                'requests' => $requests,
                'total_count' => $requests->count(),
                'status_filter' => $status,
            ]
        ]);
    }

    /**
     * Get statistics for company request letters
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $company = $request->get('api_company');
        
        // Get users from this company
        $companyUserIds = $company->users()->pluck('id');

        $stats = [
            'total' => RequestLegalLetter::whereIn('requested_by', $companyUserIds)->count(),
            'pending' => RequestLegalLetter::whereIn('requested_by', $companyUserIds)->where('status', 'Pending')->count(),
            'processing' => RequestLegalLetter::whereIn('requested_by', $companyUserIds)->where('status', 'Processing')->count(),
            'completed' => RequestLegalLetter::whereIn('requested_by', $companyUserIds)->where('status', 'Completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'code' => $company->code,
                ],
                'statistics' => $stats,
            ]
        ]);
    }

    /**
     * Get a specific request letter by ID (if it belongs to the company)
     */
    public function show(Request $request, $id): JsonResponse
    {
        $company = $request->get('api_company');
        
        // Get users from this company
        $companyUserIds = $company->users()->pluck('id');

        $requestLetter = RequestLegalLetter::whereIn('requested_by', $companyUserIds)
            ->with(['requester:id,name,email', 'assignedCompany:id,name,code', 'legalLetter'])
            ->find($id);

        if (!$requestLetter) {
            return response()->json([
                'success' => false,
                'message' => 'Request letter not found or does not belong to your company'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'code' => $company->code,
                ],
                'request' => $requestLetter,
            ]
        ]);
    }
}
