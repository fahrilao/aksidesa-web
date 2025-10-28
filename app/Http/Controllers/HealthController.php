<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    /**
     * Health check endpoint for Docker
     */
    public function check()
    {
        $health = [
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'services' => []
        ];

        // Check database connection
        try {
            DB::connection()->getPdo();
            $health['services']['database'] = 'ok';
        } catch (\Exception $e) {
            $health['services']['database'] = 'error';
            $health['status'] = 'error';
        }

        // Check Redis connection
        try {
            Redis::ping();
            $health['services']['redis'] = 'ok';
        } catch (\Exception $e) {
            $health['services']['redis'] = 'error';
            $health['status'] = 'warning';
        }

        // Check storage directory
        if (is_writable(storage_path())) {
            $health['services']['storage'] = 'ok';
        } else {
            $health['services']['storage'] = 'error';
            $health['status'] = 'error';
        }

        $statusCode = $health['status'] === 'ok' ? 200 : 503;

        return response()->json($health, $statusCode);
    }
}
