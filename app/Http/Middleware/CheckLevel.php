<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLevel
{
    public function handle(Request $request, Closure $next, ...$levels): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array((string) Auth::user()->level, $levels, true)) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak punya hak akses');
        }

        return $next($request);
    }
}
