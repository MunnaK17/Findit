<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLaporan   = Report::count();
        $totalPending   = Report::where('status', 'pending')->count();
        $totalApproved  = Report::where('status', 'approved')->count();
        $totalCompleted = Report::where('status', 'completed')->count();
        $totalKlaim     = Claim::count();
        $totalKlaimPending = Claim::where('status_klaim', 'pending')->count();
        $totalUser      = User::where('role', 'mahasiswa')->count();

        $laporanTerbaru = Report::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $klaimTerbaru = Claim::with(['user', 'report'])
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
            'laporanTerbaru',
            'klaimTerbaru'
        ));
    }
}