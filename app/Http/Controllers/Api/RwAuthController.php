<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RequestLegalLetter;
use App\Models\LegalLetter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RwAuthController extends Controller
{
    /**
     * Login for RW users only
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Only allow RW users to login via this API
        if ($user->role !== 'RW') {
            return response()->json([
                'success' => false,
                'message' => 'Only RW users can login via this API'
            ], 403);
        }

        // Create token for the user
        $token = $user->createToken('RW-API-Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'company' => $user->company ? [
                        'id' => $user->company->id,
                        'name' => $user->company->name,
                        'code' => $user->company->code,
                    ] : null,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Logout the authenticated user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get the authenticated user's profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'company' => $user->company ? [
                        'id' => $user->company->id,
                        'name' => $user->company->name,
                        'code' => $user->company->code,
                    ] : null,
                ],
            ]
        ]);
    }

    /**
     * Create a new request legal letter
     */
    public function createRequest(Request $request): JsonResponse
    {
        $user = $request->user();

        // Ensure user is RW
        if ($user->role !== 'RW') {
            return response()->json([
                'success' => false,
                'message' => 'Only RW users can create legal letter requests'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'description' => 'required|string',
            'ktp_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'kk_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file uploads
        $ktpImagePath = null;
        $kkImagePath = null;

        if ($request->hasFile('ktp_image')) {
            $ktpImagePath = $request->file('ktp_image')->store('documents/ktp', 'public');
        }

        if ($request->hasFile('kk_image')) {
            $kkImagePath = $request->file('kk_image')->store('documents/kk', 'public');
        }

        // Auto-assign to the user's company if they have one
        $requestLetter = RequestLegalLetter::create([
            'title' => $request->title,
            'name' => $request->name,
            'nik' => $request->nik,
            'description' => $request->description,
            'ktp_image_path' => $ktpImagePath,
            'kk_image_path' => $kkImagePath,
            'requested_by' => $user->id,
            'assigned_company_id' => $user->company_id,
            'status' => 'Waiting',
        ]);

        $requestLetter->load(['requester:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Legal letter request created successfully',
            'data' => $requestLetter
        ], 201);
    }

    /**
     * Get user's request letters by status
     */
    public function getRequestsByStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status');

        // Validate status parameter
        $validStatuses = ['Pending', 'Processing', 'Completed'];
        if ($status && !in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status. Valid statuses are: ' . implode(', ', $validStatuses)
            ], 400);
        }

        $query = RequestLegalLetter::where('requested_by', $user->id)
            ->with([
                'requester:id,name,email', 
                'legalLetter:id,title', 
                'requestedLegalLetter:id,title,description',
                'assignedCompany:id,name,code'
            ]);

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'requests' => $requests,
                'total_count' => $requests->count(),
                'status_filter' => $status,
            ]
        ]);
    }

    /**
     * Get a specific request by ID (only user's own requests)
     */
    public function getRequest(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $requestLetter = RequestLegalLetter::where('requested_by', $user->id)
            ->with([
                'requester:id,name,email', 
                'legalLetter', 
                'requestedLegalLetter:id,title,description',
                'assignedCompany:id,name,code'
            ])
            ->find($id);

        if (!$requestLetter) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found or you do not have permission to view it'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $requestLetter
        ]);
    }

    /**
     * Get legal letters related to the user's company
     */
    public function getLegalLetters(Request $request): JsonResponse
    {
        $user = $request->user();

        // Ensure user is RW and has a company
        if ($user->role !== 'RW') {
            return response()->json([
                'success' => false,
                'message' => 'Only RW users can access legal letters'
            ], 403);
        }

        if (!$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'User must be associated with a company to access legal letters'
            ], 403);
        }

        // Get query parameters
        $status = $request->query('status');
        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);

        // Validate status parameter
        $validStatuses = ['active', 'inactive'];
        if ($status && !in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status. Valid statuses are: ' . implode(', ', $validStatuses)
            ], 400);
        }

        // Build query for legal letters associated with the user's company
        $query = LegalLetter::whereHas('companies', function ($q) use ($user, $status) {
            $q->where('company_id', $user->company_id);
            if ($status) {
                $q->where('legal_letter_company.status', $status);
            }
        })->with([
            'creator:id,name,email',
            'companies' => function ($q) use ($user) {
                $q->where('company_id', $user->company_id)
                  ->withPivot(['status', 'notes', 'activated_at', 'deactivated_at', 'updated_by']);
            }
        ]);

        // Paginate results
        $legalLetters = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // Load the user's company relationship
        $user->load('company');

        return response()->json([
            'success' => true,
            'data' => [
                'legal_letters' => $legalLetters->items(),
                'pagination' => [
                    'current_page' => $legalLetters->currentPage(),
                    'per_page' => $legalLetters->perPage(),
                    'total' => $legalLetters->total(),
                    'last_page' => $legalLetters->lastPage(),
                    'from' => $legalLetters->firstItem(),
                    'to' => $legalLetters->lastItem(),
                ],
                'company' => [
                    'id' => $user->company->id,
                    'name' => $user->company->name,
                    'code' => $user->company->code,
                ],
                'filters' => [
                    'status' => $status,
                ]
            ]
        ]);
    }

    /**
     * Get a specific legal letter by ID (only if associated with user's company)
     */
    public function getLegalLetter(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        // Ensure user is RW and has a company
        if ($user->role !== 'RW') {
            return response()->json([
                'success' => false,
                'message' => 'Only RW users can access legal letters'
            ], 403);
        }

        if (!$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'User must be associated with a company to access legal letters'
            ], 403);
        }

        // Find legal letter associated with user's company
        $legalLetter = LegalLetter::whereHas('companies', function ($q) use ($user) {
            $q->where('company_id', $user->company_id);
        })->with([
            'creator:id,name,email',
            'companies' => function ($q) use ($user) {
                $q->where('company_id', $user->company_id)
                  ->withPivot(['status', 'notes', 'activated_at', 'deactivated_at', 'updated_by']);
            }
        ])->find($id);

        if (!$legalLetter) {
            return response()->json([
                'success' => false,
                'message' => 'Legal letter not found or you do not have permission to view it'
            ], 404);
        }

        // Load the user's company relationship
        $user->load('company');

        return response()->json([
            'success' => true,
            'data' => [
                'legal_letter' => $legalLetter,
                'company' => [
                    'id' => $user->company->id,
                    'name' => $user->company->name,
                    'code' => $user->company->code,
                ]
            ]
        ]);
    }
}
