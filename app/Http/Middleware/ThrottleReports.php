<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class ThrottleReports
{
    public function __construct(protected RateLimiter $limiter)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $key = 'report-create:' . auth()->id();
        
        if ($this->limiter->tooManyAttempts($key, 5, 60)) {
            return redirect()->back()
                ->with('error', 'Terlalu banyak laporan dalam waktu singkat. Coba lagi dalam 1 menit.');
        }

        $this->limiter->hit($key, 60);

        return $next($request);
    }
}
