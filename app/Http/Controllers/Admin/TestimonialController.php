<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Tampilkan semua testimoni
     */
    public function index(Request $request)
    {
        $query = Testimonial::with(['user', 'claim', 'report']);

        // Filter berdasarkan rating
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        // Filter berdasarkan pencarian
        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('isi_testimoni', 'like', $search)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', $search)
                         ->orWhere('nim', 'like', $search);
                  });
            });
        }

        $testimonials = $query->latest()->paginate(15);

        // Statistik
        $stats = [
            'total' => Testimonial::count(),
            'avg_rating' => Testimonial::avg('rating') ? round(Testimonial::avg('rating'), 1) : 0,
            'rating_5' => Testimonial::where('rating', 5)->count(),
            'rating_1' => Testimonial::where('rating', 1)->count(),
        ];

        return view('admin.testimonials.index', compact('testimonials', 'stats'));
    }

    /**
     * Hapus testimoni
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);

        // Jika testimoni terkait dengan klaim, update status klaim
        if ($testimonial->id_claim) {
            // Soft delete - klaim tetap ada, hanya testimoni yang dihapus
        }

        $testimonial->delete();

        return redirect()->back()
            ->with('success', 'Testimoni berhasil dihapus.');
    }
}