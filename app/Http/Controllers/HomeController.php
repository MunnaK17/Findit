<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil 6 laporan temuan terbaru yang approved untuk ditampilkan di landing page
        $laporanTemuan = Report::with(['category', 'user'])
            ->where('jenis_laporan', 'temuan')
            ->where('status', 'approved')
            ->latest()
            ->take(6)
            ->get();

        $laporanHilang = Report::with(['category', 'user'])
            ->where('jenis_laporan', 'hilang')
            ->where('status', 'approved')
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('laporanTemuan', 'laporanHilang'));
    }
}