<?php

namespace App\Http\Controllers;

use App\Models\RequestLegalLetter;
use App\Models\LegalLetter;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        
        // Get statistics based on user role
        if ($user->isAdministrator()) {
            $stats = $this->getAdminStats();
        } elseif ($user->isOperator()) {
            $stats = $this->getOperatorStats($user);
        } else {
            // Fallback for other roles
            $stats = $this->getBasicStats();
        }
        
        return view('home', compact('stats'));
    }
    
    private function getAdminStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_requests' => RequestLegalLetter::count(),
            'pending_requests' => RequestLegalLetter::where('status', 'Pending')->count(),
            'processing_requests' => RequestLegalLetter::where('status', 'Processing')->count(),
            'completed_requests' => RequestLegalLetter::where('status', 'Completed')->count(),
            'requests_today' => RequestLegalLetter::whereDate('created_at', $today)->count(),
            'completed_today' => RequestLegalLetter::where('status', 'Completed')
                ->whereDate('updated_at', $today)->count(),
            'completed_this_month' => RequestLegalLetter::where('status', 'Completed')
                ->where('updated_at', '>=', $thisMonth)->count(),
            'total_companies' => Company::count(),
            'total_operators' => User::where('role', 'Operator')->count(),
            'total_rw_users' => User::where('role', 'RW')->count(),
        ];
    }
    
    private function getOperatorStats($user)
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_requests' => RequestLegalLetter::where('assigned_company_id', $user->company_id)->count(),
            'pending_requests' => RequestLegalLetter::where('assigned_company_id', $user->company_id)
                ->where('status', 'Pending')->count(),
            'processing_requests' => RequestLegalLetter::where('assigned_company_id', $user->company_id)
                ->where('status', 'Processing')->count(),
            'completed_requests' => RequestLegalLetter::where('assigned_company_id', $user->company_id)
                ->where('status', 'Completed')->count(),
            'requests_today' => RequestLegalLetter::where('assigned_company_id', $user->company_id)
                ->whereDate('created_at', $today)->count(),
            'completed_today' => RequestLegalLetter::where('assigned_company_id', $user->company_id)
                ->where('status', 'Completed')
                ->whereDate('updated_at', $today)->count(),
            'completed_this_month' => RequestLegalLetter::where('assigned_company_id', $user->company_id)
                ->where('status', 'Completed')
                ->where('updated_at', '>=', $thisMonth)->count(),
        ];
    }
    
    private function getBasicStats()
    {
        return [
            'total_requests' => RequestLegalLetter::count(),
            'pending_requests' => RequestLegalLetter::where('status', 'Pending')->count(),
            'processing_requests' => RequestLegalLetter::where('status', 'Processing')->count(),
            'completed_requests' => RequestLegalLetter::where('status', 'Completed')->count(),
            'requests_today' => 0,
            'completed_today' => 0,
            'completed_this_month' => 0,
        ];
    }
}
