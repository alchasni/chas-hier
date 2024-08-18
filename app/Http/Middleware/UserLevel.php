<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserLevel
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed $level  [1. admin | 2. kasir]
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$level)
    {
        if (auth()->user() && in_array(auth()->user()->level, $level)) {
            return $next($request);
        }
        return redirect()->route('dashboard')->withErrors(['error' => 'You cannot access that']);
    }
}
