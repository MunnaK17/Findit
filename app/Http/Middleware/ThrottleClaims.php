<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class ThrottleClaims
{
    public function __construct(protected RateLimiter $limiter)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $key = 'claim-create:' . auth()->id();
        
        if ($this->limiter->tooManyAttempts($key, 10, 60)) {
            return redirect()->back()
                ->with('error', 'Terlalu banyak klaim dalam waktu singkat. Coba lagi dalam 1 menit.');
        }

        $this->limiter->hit($key, 60);

        return $next($request);
    }
}
