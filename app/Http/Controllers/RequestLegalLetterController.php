<?php

namespace App\Http\Controllers;

use App\Models\RequestLegalLetter;
use App\Models\LegalLetter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequestLegalLetterController extends Controller
{
    /**
     * Display a listing of requests (for Operators - see assigned requests, for RW - see own requests)
     */
    public function index(Request $request)
    {
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $user = Auth::user();
            
            if ($user->isAdministrator()) {
                // Administrators can see all requests
                $query = RequestLegalLetter::with(['requester', 'assignedCompany', 'legalLetter']);
            } elseif ($user->isOperator()) {
                // Operators can see requests assigned to their company or unassigned requests
                $query = RequestLegalLetter::with(['requester', 'assignedCompany', 'legalLetter'])
                    ->where(function($q) use ($user) {
                        $q->where('assigned_company_id', $user->company_id)
                          ->orWhereNull('assigned_company_id');
                    });
            } else {
                // RW users can only see their own requests
                $query = RequestLegalLetter::with(['requester', 'assignedCompany', 'legalLetter'])
                    ->where('requested_by', $user->id);
            }

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $requests = $query->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $requests
            ]);
        }

        // For web requests, return the view
        return view('request-legal-letters.index');
    }

    /**
     * Store a new request (RW users can create requests)
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Only RW users can create requests
        if ($user->role !== 'RW') {
            return response()->json([
                'success' => false,
                'message' => 'Only RW users can create legal letter requests'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:request_legal_letters,nik',
            'description' => 'required|string',
            'ktp_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'kk_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
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
        $legalLetterRequest = RequestLegalLetter::create([
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

        $legalLetterRequest->load(['requester', 'assignedCompany', 'legalLetter']);

        return response()->json([
            'success' => true,
            'message' => 'Legal letter request created successfully',
            'data' => $legalLetterRequest
        ], 201);
    }

    /**
     * Display the specified request
     */
    public function show(Request $request, RequestLegalLetter $requestLegalLetter)
    {
        $user = Auth::user();
        
        // Check authorization
        if (!$user->isAdministrator() && 
            !($user->isOperator() && ($requestLegalLetter->assigned_company_id === $user->company_id || $requestLegalLetter->assigned_company_id === null)) &&
            !($user->role === 'RW' && $requestLegalLetter->requested_by === $user->id)) {
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this request'
                ], 403);
            }
            
            abort(403, 'Unauthorized to view this request');
        }

        $requestLegalLetter->load(['requester', 'assignedCompany', 'legalLetter']);

        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $requestLegalLetter
            ]);
        }

        // For web requests, return the view
        return view('request-legal-letters.show', compact('requestLegalLetter'));
    }

    /**
     * Assign request to operator (Operators can assign to themselves)
     */
    public function assignToSelf(RequestLegalLetter $requestLegalLetter): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            return response()->json([
                'success' => false,
                'message' => 'Only operators can assign requests to themselves'
            ], 403);
        }

        if ($requestLegalLetter->assigned_company_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Request is already assigned to a company'
            ], 400);
        }

        $requestLegalLetter->update([
            'assigned_company_id' => $user->company_id,
            'status' => 'Processing'
        ]);

        $requestLegalLetter->load(['requester', 'assignedCompany', 'legalLetter']);

        return response()->json([
            'success' => true,
            'message' => 'Request assigned successfully',
            'data' => $requestLegalLetter
        ]);
    }

    /**
     * Update request status (Operators can change status)
     */
    public function updateStatus(Request $request, RequestLegalLetter $requestLegalLetter): JsonResponse
    {
        $user = Auth::user();
        
        // Only operators from the assigned company can update status
        if (!$user->isOperator() || $requestLegalLetter->assigned_company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Only operators from the assigned company can update request status'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Waiting,Pending,Processing,Completed',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $requestLegalLetter->update([
            'status' => $request->status
        ]);

        $requestLegalLetter->load(['requester', 'assignedCompany', 'legalLetter']);

        return response()->json([
            'success' => true,
            'message' => 'Request status updated successfully',
            'data' => $requestLegalLetter
        ]);
    }

    /**
     * Complete request by creating a legal letter (Operators)
     */
    public function complete(Request $request, RequestLegalLetter $requestLegalLetter): JsonResponse
    {
        $user = Auth::user();
        
        // Only operators from the assigned company can complete it
        if (!$user->isOperator() || $requestLegalLetter->assigned_company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Only operators from the assigned company can complete this request'
            ], 403);
        }

        if ($requestLegalLetter->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Request is already completed'
            ], 400);
        }

        // Create the legal letter
        $legalLetter = LegalLetter::create([
            'title' => $requestLegalLetter->title,
            'description' => $requestLegalLetter->description,
            'created_by' => $user->id,
        ]);

        // Update the request
        $requestLegalLetter->update([
            'status' => 'Completed',
            'legal_letter_id' => $legalLetter->id
        ]);

        $requestLegalLetter->load(['requester', 'assignedCompany', 'legalLetter']);

        return response()->json([
            'success' => true,
            'message' => 'Request completed and legal letter created successfully',
            'data' => $requestLegalLetter
        ]);
    }

    /**
     * Get statistics for requests
     */
    public function statistics(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->isAdministrator()) {
            $total = RequestLegalLetter::count();
            $byStatus = [
                'Pending' => RequestLegalLetter::pending()->count(),
                'Processing' => RequestLegalLetter::processing()->count(),
                'Completed' => RequestLegalLetter::completed()->count(),
            ];
        } elseif ($user->isOperator()) {
            $total = RequestLegalLetter::assignedToCompany($user->company_id)->count();
            $byStatus = [
                'Pending' => RequestLegalLetter::assignedToCompany($user->company_id)->pending()->count(),
                'Processing' => RequestLegalLetter::assignedToCompany($user->company_id)->processing()->count(),
                'Completed' => RequestLegalLetter::assignedToCompany($user->company_id)->completed()->count(),
            ];
        } else {
            $total = RequestLegalLetter::requestedBy($user->id)->count();
            $byStatus = [
                'Pending' => RequestLegalLetter::requestedBy($user->id)->pending()->count(),
                'Processing' => RequestLegalLetter::requestedBy($user->id)->processing()->count(),
                'Completed' => RequestLegalLetter::requestedBy($user->id)->completed()->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'by_status' => $byStatus,
            ]
        ]);
    }
}
