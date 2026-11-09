<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic stats
        $totalLaporan   = Report::count();
        $totalPending   = Report::where('status', 'pending')->count();
        $totalApproved  = Report::where('status', 'approved')->count();
        $totalCompleted = Report::where('status', 'completed')->count();
        $totalKlaim     = Claim::count();
        $totalKlaimPending = Claim::where('status_klaim', 'pending')->count();
        $totalUser      = User::where('role', 'mahasiswa')->count();

        // Priority stats - count reports by category priority
        $criticalCount = Report::whereHas('category', function ($q) {
            $q->where('priority', 'critical');
        })->count();

        $highCount = Report::whereHas('category', function ($q) {
            $q->where('priority', 'high');
        })->count();

        $normalCount = Report::whereHas('category', function ($q) {
            $q->where('priority', 'normal');
        })->count();

        // Reports with pending claims count
        $reportsWithPendingClaims = Report::whereHas('claims', function ($q) {
            $q->where('status_klaim', 'pending');
        })->count();

        // Latest reports with user, category, and claims count
        $laporanTerbaru = Report::with(['user', 'category', 'claims'])
            ->latest()
            ->take(5)
            ->get();

        // Latest claims with user and report (including owner info)
        $klaimTerbaru = Claim::with(['user', 'report.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalLaporan',
            'totalPending',
            'totalApproved',
            'totalCompleted',
            'totalKlaim',
            'totalKlaimPending',
            'totalUser',
            'criticalCount',
            'highCount',
            'normalCount',
            'reportsWithPendingClaims',
            'laporanTerbaru',
            'klaimTerbaru'
        ));
    }
}