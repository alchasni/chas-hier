<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserCSRF
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $currentSessionId = session()->getId();
        $storedSessionId = $user ? $user->session_token : null;


        if ($user && $storedSessionId === $currentSessionId) {
            return $next($request);
        }

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->withErrors('Your session has expired.');
    }
}
