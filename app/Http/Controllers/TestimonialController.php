<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    /**
     * Tampilkan form buat testimoni untuk klaim tertentu
     */
    public function create($claimId)
    {
        $claim = Claim::with(['report', 'user'])->findOrFail($claimId);

        // Cek apakah klaim milik user yang login
        if ($claim->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke klaim ini.');
        }

        // Cek apakah klaim berstatus approved
        if ($claim->status_klaim !== 'approved') {
            return redirect()->route('my.claims')
                ->with('error', 'Anda hanya bisa memberikan testimoni untuk klaim yang sudah disetujui.');
        }

        // Cek apakah sudah ada testimoni untuk klaim ini
        $sudahAda = Testimonial::where('id_claim', $claimId)->exists();
        if ($sudahAda) {
            return redirect()->route('my.claims')
                ->with('error', 'Anda sudah memberikan testimoni untuk klaim ini.');
        }

        return view('testimonials.create', compact('claim'));
    }

    /**
     * Simpan testimoni baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_claim'      => ['required', 'exists:claims,id'],
            'id_report'     => ['required', 'exists:reports,id'],
            'isi_testimoni' => ['required', 'string', 'max:500'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5', 'gt:0'],
        ], [
            'rating.min' => 'Silakan pilih rating bintang.',
            'rating.gt' => 'Silakan pilih rating bintang.',
        ]);

        $claim = Claim::findOrFail($request->id_claim);

        // Double check: klaim milik user
        if ($claim->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke klaim ini.');
        }

        // Double check: klaim approved
        if ($claim->status_klaim !== 'approved') {
            return redirect()->route('my.claims')
                ->with('error', 'Anda hanya bisa memberikan testimoni untuk klaim yang sudah disetujui.');
        }

        // Double check: belum ada testimoni
        $sudahAda = Testimonial::where('id_claim', $request->id_claim)->exists();
        if ($sudahAda) {
            return redirect()->route('my.claims')
                ->with('error', 'Anda sudah memberikan testimoni untuk klaim ini.');
        }

        Testimonial::create([
            'id_user'        => Auth::id(),
            'id_claim'       => $request->id_claim,
            'id_report'      => $request->id_report,
            'isi_testimoni'  => $request->isi_testimoni,
            'rating'         => $request->rating,
        ]);

        return redirect()->route('my.testimonials')
            ->with('success', 'Terima kasih! Testimoni Anda berhasil dikirim.');
    }

    /**
     * Tampilkan daftar testimoni user
     */
    public function myTestimonials()
    {
        $testimonials = Testimonial::with(['claim', 'report'])
            ->where('id_user', Auth::id())
            ->latest()
            ->paginate(10);

        return view('testimonials.my-testimonials', compact('testimonials'));
    }

    /**
     * Tampilkan form edit testimoni
     */
    public function edit($id)
    {
        $testimonial = Testimonial::with('report')
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        return view('testimonials.edit', compact('testimonial'));
    }

    /**
     * Update testimoni
     */
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::where('id_user', Auth::id())->findOrFail($id);

        $request->validate([
            'isi_testimoni' => ['required', 'string', 'max:500'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5', 'gt:0'],
        ], [
            'rating.min' => 'Silakan pilih rating bintang.',
            'rating.gt' => 'Silakan pilih rating bintang.',
        ]);

        $testimonial->update([
            'isi_testimoni' => $request->isi_testimoni,
            'rating'        => $request->rating,
        ]);

        return redirect()->route('my.testimonials')
            ->with('success', 'Testimoni berhasil diperbarui.');
    }

    /**
     * Hapus testimoni
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::where('id_user', Auth::id())->findOrFail($id);

        $testimonial->delete();

        return redirect()->route('my.testimonials')
            ->with('success', 'Testimoni berhasil dihapus.');
    }
}