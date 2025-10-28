<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Api\CompanyRequestLetterController;
use App\Http\Controllers\Api\RwAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// RW User Authentication API
Route::prefix('rw')->group(function () {
    Route::post('login', [RwAuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [RwAuthController::class, 'logout']);
        Route::get('profile', [RwAuthController::class, 'profile']);
        Route::post('requests', [RwAuthController::class, 'createRequest']);
        Route::get('requests', [RwAuthController::class, 'getRequestsByStatus']);
        Route::get('requests/{id}', [RwAuthController::class, 'getRequest']);
        Route::get('legal-letters', [RwAuthController::class, 'getLegalLetters']);
        Route::get('legal-letters/{id}', [RwAuthController::class, 'getLegalLetter']);
    });
});

// Company API Key Routes (for external company access)
Route::prefix('company')->middleware('api.key')->group(function () {
    Route::get('requests', [CompanyRequestLetterController::class, 'getByStatus']);
    Route::get('requests/statistics', [CompanyRequestLetterController::class, 'getStatistics']);
    Route::get('requests/{id}', [CompanyRequestLetterController::class, 'show']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Health check endpoint for Docker
Route::get('/health', [HealthController::class, 'check']);
