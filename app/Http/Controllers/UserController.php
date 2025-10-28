<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $query = User::with('company:id,name,code');
            
            // Filter by role if provided
            if ($request->has('role') && in_array($request->role, User::getRoles())) {
                $query->where('role', $request->role);
            }
            
            // Filter by company if provided
            if ($request->has('company_id')) {
                $query->where('company_id', $request->company_id);
            }
            
            // Search by name or email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            $users = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json([
                'success' => true,
                'data' => $users,
                'roles' => User::getRoles()
            ]);
        }

        // For web requests, return the view
        return view('users.index');
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->load('company:id,name,code');

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, User $user)
    {
        // If it's an AJAX request, return JSON for the frontend
        if ($request->expectsJson()) {
            $user->load('company:id,name,code');
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }

        // For web requests, return the view
        return view('users.show', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->load('company:id,name,code');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deletion of the last Administrator
        if ($user->isAdministrator()) {
            $adminCount = User::where('role', 'Administrator')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the last Administrator'
                ], 422);
            }
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get users by role (API only)
     */
    public function getUsersByRole(string $role)
    {
        if (!in_array($role, User::getRoles())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role specified'
            ], 400);
        }

        $users = User::with('company:id,name,code')
            ->where('role', $role)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(User::getRoles())],
            'company_id' => [
                'nullable',
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($request) {
                    // Administrator should not have company_id
                    if ($request->role === 'Administrator' && $value !== null) {
                        $fail('Administrator users should not be assigned to a company.');
                    }
                    // Non-Administrator users must have company_id
                    if ($request->role !== 'Administrator' && $value === null) {
                        $fail('This user role requires a company assignment.');
                    }
                },
            ],
        ]);

        // Prevent changing role of the last Administrator
        if ($user->isAdministrator() && $validated['role'] !== 'Administrator') {
            $adminCount = User::where('role', 'Administrator')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot change role of the last Administrator'
                ], 422);
            }
        }

        // Set company_id to null if changing to Administrator
        if ($validated['role'] === 'Administrator') {
            $validated['company_id'] = null;
        }

        $user->update($validated);
        $user->load('company:id,name,code');

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Bulk update user roles
     */
    public function bulkUpdateRoles(Request $request)
    {
        $validated = $request->validate([
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
            'users.*.role' => ['required', Rule::in(User::getRoles())],
            'users.*.company_id' => 'nullable|exists:companies,id',
        ]);

        $updated = [];
        $errors = [];

        foreach ($validated['users'] as $userData) {
            $user = User::find($userData['id']);
            
            // Check if trying to change last Administrator
            if ($user->isAdministrator() && $userData['role'] !== 'Administrator') {
                $adminCount = User::where('role', 'Administrator')->count();
                if ($adminCount <= 1) {
                    $errors[] = "Cannot change role of user {$user->name} (last Administrator)";
                    continue;
                }
            }

            // Validate company assignment based on role
            if ($userData['role'] === 'Administrator') {
                $userData['company_id'] = null;
            } elseif (empty($userData['company_id'])) {
                $errors[] = "User {$user->name} requires a company assignment for role {$userData['role']}";
                continue;
            }

            $user->update([
                'role' => $userData['role'],
                'company_id' => $userData['company_id']
            ]);
            
            $user->load('company:id,name,code');
            $updated[] = $user;
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk role update completed',
            'updated' => $updated,
            'errors' => $errors
        ]);
    }
}
