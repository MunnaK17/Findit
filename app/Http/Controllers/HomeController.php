<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $campusId = $request->filled('campus') ? (int) $request->campus : null;

        $latest = fn (string $jenis) => Report::with(['category', 'user', 'campus'])
            ->where('jenis_laporan', $jenis)
            ->where('status', 'approved')
            ->when($campusId, fn ($q) => $q->where('id_campus', $campusId))
            ->latest()
            ->take(6)
            ->get();

        $laporanTemuan = $latest('temuan');
        $laporanHilang = $latest('hilang');

        return view('home', compact('laporanTemuan', 'laporanHilang', 'campusId'));
    }
}
