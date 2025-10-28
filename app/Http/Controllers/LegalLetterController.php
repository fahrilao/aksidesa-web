<?php

namespace App\Http\Controllers;

use App\Models\LegalLetter;
use App\Models\User;
use App\Http\Requests\StoreLegalLetterRequest;
use App\Http\Requests\UpdateLegalLetterRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LegalLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $query = LegalLetter::with(['creator']);

            // Apply filters
            if ($request->has('created_by')) {
                $query->where('created_by', $request->created_by);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $requests = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $requests,
            ]);
        }

        // For web requests, return the view
        return view('legal-letters.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLegalLetterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        $legalRequest = LegalLetter::create($data);
        $legalRequest->load(['creator']);

        return response()->json([
            'success' => true,
            'message' => 'Legal letter request created successfully',
            'data' => $legalRequest,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, LegalLetter $legalLetter)
    {
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $legalLetter->load(['creator']);

            return response()->json([
                'success' => true,
                'data' => $legalLetter,
            ]);
        }

        // For web requests, return the view
        return view('legal-letters.show', compact('legalLetter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLegalLetterRequest $request, LegalLetter $legalLetter): JsonResponse
    {
        $legalLetter->update($request->validated());
        $legalLetter->load(['creator']);

        return response()->json([
            'success' => true,
            'message' => 'Legal letter request updated successfully',
            'data' => $legalLetter,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LegalLetter $legalLetter): JsonResponse
    {
        $legalLetter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Legal letter request deleted successfully',
        ]);
    }

    /**
     * Get all users for assignment dropdown
     */
    public function getUsers(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'role')->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }


    /**
     * Get statistics
     */
    public function getStatistics(): JsonResponse
    {
        $stats = [
            'total' => LegalLetter::count(),
            'by_creator' => LegalLetter::selectRaw('created_by, COUNT(*) as count')
                ->groupBy('created_by')
                ->with('creator:id,name')
                ->get()
                ->pluck('count', 'creator.name'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Attach companies to a legal letter request
     */
    public function attachCompanies(Request $request, LegalLetter $legalLetter): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_ids' => 'required|array',
            'company_ids.*' => 'exists:companies,id',
            'status' => 'sometimes|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = $request->get('status', 'active');
        $notes = $request->get('notes');
        $userId = Auth::id();

        foreach ($request->company_ids as $companyId) {
            // Check if already attached
            if (!$legalLetter->companies()->where('company_id', $companyId)->exists()) {
                $legalLetter->attachCompany($companyId, $status, $notes, $userId);
            }
        }

        $legalLetter->load(['companies' => function ($query) {
            $query->withPivot(['status', 'notes', 'activated_at', 'deactivated_at', 'updated_by']);
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Companies attached successfully',
            'data' => $legalLetter,
        ]);
    }

    /**
     * Toggle company status for a legal letter request (for operators)
     */
    public function toggleCompanyStatus(Request $request, LegalLetter $legalLetter, $companyId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        
        // Check if user is operator and if the company belongs to them
        if ($user->role === 'Operator' && $user->company_id != $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only manage status for your own company',
            ], 403);
        }

        // Check if the company is attached to this request
        if (!$legalLetter->companies()->where('company_id', $companyId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Company is not associated with this legal letter request',
            ], 404);
        }

        $status = $request->status;
        $notes = $request->get('notes');

        $legalLetter->updateCompanyStatus($companyId, $status, $notes, $user->id);

        // Load the updated relationship
        $company = $legalLetter->companies()
            ->where('company_id', $companyId)
            ->withPivot(['status', 'notes', 'activated_at', 'deactivated_at', 'updated_by'])
            ->first();

        return response()->json([
            'success' => true,
            'message' => "Company status updated to {$status}",
            'data' => [
                'company' => $company,
                'pivot' => $company->pivot,
            ],
        ]);
    }

    /**
     * Get companies associated with a legal letter request
     */
    public function getRequestCompanies(LegalLetter $legalLetter): JsonResponse
    {
        $companies = $legalLetter->companies()
            ->withPivot(['status', 'notes', 'activated_at', 'deactivated_at', 'updated_by'])
            ->with(['users' => function ($query) {
                $query->select('id', 'name', 'email', 'role');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $companies,
        ]);
    }

    /**
     * Get legal letter requests for a specific company (for operators)
     */
    public function getCompanyRequests(Request $request, $companyId)
    {
        $user = Auth::user();
        
        // Check if user is operator and if the company belongs to them
        if ($user->role === 'Operator' && $user->company_id != $companyId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only view requests for your own company',
                ], 403);
            }
            abort(403, 'You can only view requests for your own company');
        }

        $company = \App\Models\Company::findOrFail($companyId);
        
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $requests = $company->legalLetters()
                ->withPivot(['status', 'notes', 'activated_at', 'deactivated_at', 'updated_by'])
                ->with(['creator'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'company' => $company,
                    'requests' => $requests,
                ],
            ]);
        }

        // For web requests, return the view
        return view('companies.legal-letters', compact('company'));
    }

    /**
     * Operator index - Show all legal letters with assignment status for operator's company
     */
    public function operatorIndex(Request $request)
    {
        $user = Auth::user();
        
        // Only operators can access this
        if (!$user->isOperator()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only operators can access this page'
                ], 403);
            }
            abort(403, 'Only operators can access this page');
        }

        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $query = LegalLetter::with(['creator']);

            // Apply search filter
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply assignment filter
            if ($request->has('assignment_status')) {
                $companyId = $user->company_id;
                if ($request->assignment_status === 'assigned') {
                    $query->whereHas('companies', function($q) use ($companyId) {
                        $q->where('company_id', $companyId);
                    });
                } elseif ($request->assignment_status === 'unassigned') {
                    $query->whereDoesntHave('companies', function($q) use ($companyId) {
                        $q->where('company_id', $companyId);
                    });
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $legalLetters = $query->paginate(15);

            // Add assignment status for each legal letter
            foreach ($legalLetters as $letter) {
                $assignment = $letter->companies()->where('company_id', $user->company_id)->first();
                $letter->is_assigned = $assignment ? true : false;
                $letter->assignment_status = $assignment ? $assignment->pivot->status : null;
                $letter->assignment_notes = $assignment ? $assignment->pivot->notes : null;
                $letter->assignment_updated_at = $assignment ? $assignment->pivot->updated_at : null;
            }

            return response()->json([
                'success' => true,
                'data' => $legalLetters
            ]);
        }

        // For web requests, return the view
        return view('legal-letters.operator');
    }

    /**
     * Assign legal letter to operator's company
     */
    public function assignToCompany(Request $request, LegalLetter $legalLetter): JsonResponse
    {
        $user = Auth::user();
        
        // Only operators can assign to their company
        if (!$user->isOperator()) {
            return response()->json([
                'success' => false,
                'message' => 'Only operators can assign legal letters to their company'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if already assigned
        if ($legalLetter->companies()->where('company_id', $user->company_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Legal letter is already assigned to your company'
            ], 400);
        }

        $status = $request->get('status', 'active');
        $notes = $request->get('notes');

        $legalLetter->attachCompany($user->company_id, $status, $notes, $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Legal letter assigned to your company successfully'
        ]);
    }

    /**
     * Unassign legal letter from operator's company
     */
    public function unassignFromCompany(LegalLetter $legalLetter): JsonResponse
    {
        $user = Auth::user();
        
        // Only operators can unassign from their company
        if (!$user->isOperator()) {
            return response()->json([
                'success' => false,
                'message' => 'Only operators can unassign legal letters from their company'
            ], 403);
        }

        // Check if assigned to operator's company
        if (!$legalLetter->companies()->where('company_id', $user->company_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Legal letter is not assigned to your company'
            ], 404);
        }

        $legalLetter->companies()->detach($user->company_id);

        return response()->json([
            'success' => true,
            'message' => 'Legal letter unassigned from your company successfully'
        ]);
    }

    /**
     * Detach company from legal letter request
     */
    public function detachCompany(LegalLetter $legalLetter, $companyId): JsonResponse
    {
        if (!$legalLetter->companies()->where('company_id', $companyId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Company is not associated with this legal letter request',
            ], 404);
        }

        $legalLetter->companies()->detach($companyId);

        return response()->json([
            'success' => true,
            'message' => 'Company detached successfully',
        ]);
    }
}
