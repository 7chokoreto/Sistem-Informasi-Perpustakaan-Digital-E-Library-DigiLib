<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            // Redirect sesuai role masing-masing jika coba akses role lain
            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            } elseif ($user->role === 'pustakawan') {
                return redirect()->route('pustakawan.dashboard');
            } else {
                return redirect()->route('member.dashboard');
            }
        }

        return $next($request);
    }
}
