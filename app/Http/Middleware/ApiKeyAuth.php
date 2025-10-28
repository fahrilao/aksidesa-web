<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required. Provide it via X-API-Key header or api_key query parameter.'
            ], 401);
        }

        $company = Company::findByApiKey($apiKey);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key or company is inactive.'
            ], 401);
        }

        // Update last used timestamp
        $company->updateApiKeyLastUsed();

        // Add company to request for use in controllers
        $request->merge(['api_company' => $company]);

        return $next($request);
    }
}
