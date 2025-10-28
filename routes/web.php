<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalLetterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestLegalLetterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApiKeyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('auth', [AuthController::class, 'login'])->name('auth.login');
Route::post('auth', [AuthController::class, 'authenticate'])->name('auth.authenticate');
Route::get('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

// User Management Routes (Protected by authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('', HomeController::class)->name('home');
    
    // Profile management
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Administrator only routes
    Route::middleware(['role:Administrator'])->group(function () {
        // User management
        Route::resource('users', UserController::class);
        Route::put('users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::post('users/bulk-roles', [UserController::class, 'bulkUpdateRoles'])->name('users.bulk-roles');
        
        // Company management
        Route::resource('companies', CompanyController::class);
        Route::put('companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
        Route::get('companies/{company}/users', [CompanyController::class, 'getUsers'])->name('companies.users');
        
        // Legal Letter management - Only Administrator can create
        Route::post('legal-letters', [LegalLetterController::class, 'store'])->name('legal-letters.store');
        Route::delete('legal-letters/{legalLetter}', [LegalLetterController::class, 'destroy'])->name('legal-letters.destroy');
        
        // Legal Letter - Company relationship management (Admin only)
        Route::post('legal-letters/{legalLetter}/companies', [LegalLetterController::class, 'attachCompanies'])->name('legal-letters.attach-companies');
        Route::delete('legal-letters/{legalLetter}/companies/{company}', [LegalLetterController::class, 'detachCompany'])->name('legal-letters.detach-company');
        
        // API Key management (Admin only)
        Route::get('api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
        Route::get('companies/{company}/api-key', [ApiKeyController::class, 'show'])->name('api-keys.show');
        Route::post('companies/{company}/api-key/generate', [ApiKeyController::class, 'generate'])->name('api-keys.generate');
        Route::post('companies/{company}/api-key/regenerate', [ApiKeyController::class, 'regenerate'])->name('api-keys.regenerate');
        Route::delete('companies/{company}/api-key', [ApiKeyController::class, 'revoke'])->name('api-keys.revoke');
    });
    
    // Operator and above can view users and companies
    Route::middleware(['role:Operator'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/role/{role}', [UserController::class, 'getUsersByRole'])->name('users.by-role');
        
        Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::get('companies/active/list', [CompanyController::class, 'getActiveCompanies'])->name('companies.active');
        
        // Legal Letter management - All authenticated users can view and update
        Route::get('legal-letters', [LegalLetterController::class, 'index'])->name('legal-letters.index');
        Route::get('legal-letters-users', [LegalLetterController::class, 'getUsers'])->name('legal-letters.users');
        Route::get('legal-letters-statistics', [LegalLetterController::class, 'getStatistics'])->name('legal-letters.statistics');
        
        // Operator Legal Letters - View all legal letters with assign/unassign capability (must be before parameterized routes)
        Route::get('legal-letters/operator', [LegalLetterController::class, 'operatorIndex'])->name('legal-letters.operator');
        Route::post('legal-letters/{legalLetter}/assign-company', [LegalLetterController::class, 'assignToCompany'])->name('legal-letters.assign-company');
        Route::delete('legal-letters/{legalLetter}/unassign-company', [LegalLetterController::class, 'unassignFromCompany'])->name('legal-letters.unassign-company');
        
        // Legal Letter parameterized routes (must be after specific routes)
        Route::get('legal-letters/{legalLetter}', [LegalLetterController::class, 'show'])->name('legal-letters.show');
        Route::put('legal-letters/{legalLetter}', [LegalLetterController::class, 'update'])->name('legal-letters.update');
        
        // Legal Letter - Company relationship viewing and status management
        Route::get('legal-letters/{legalLetter}/companies', [LegalLetterController::class, 'getRequestCompanies'])->name('legal-letters.companies');
        Route::put('legal-letters/{legalLetter}/companies/{company}/status', [LegalLetterController::class, 'toggleCompanyStatus'])->name('legal-letters.toggle-company-status');
        Route::get('companies/{company}/legal-letters', [LegalLetterController::class, 'getCompanyRequests'])->name('companies.legal-letters');
        
        // Request Legal Letter workflow - Operators can manage requests
        Route::get('request-legal-letters', [RequestLegalLetterController::class, 'index'])->name('request-legal-letters.index');
        Route::get('request-legal-letters/{requestLegalLetter}', [RequestLegalLetterController::class, 'show'])->name('request-legal-letters.show');
        Route::post('request-legal-letters/{requestLegalLetter}/assign', [RequestLegalLetterController::class, 'assignToSelf'])->name('request-legal-letters.assign');
        Route::put('request-legal-letters/{requestLegalLetter}/status', [RequestLegalLetterController::class, 'updateStatus'])->name('request-legal-letters.update-status');
        Route::post('request-legal-letters/{requestLegalLetter}/complete', [RequestLegalLetterController::class, 'complete'])->name('request-legal-letters.complete');
        Route::get('request-legal-letters-statistics', [RequestLegalLetterController::class, 'statistics'])->name('request-legal-letters.statistics');
    });
    
    // RW users can create requests
    Route::middleware(['auth'])->group(function () {
        Route::post('request-legal-letters', [RequestLegalLetterController::class, 'store'])->name('request-legal-letters.store');
        Route::get('request-legal-letters', [RequestLegalLetterController::class, 'index'])->name('request-legal-letters.index');
        Route::get('request-legal-letters/{requestLegalLetter}', [RequestLegalLetterController::class, 'show'])->name('request-legal-letters.show');
    });
});
