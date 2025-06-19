<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LecturerOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        
        if (!$user->employee) {
            abort(403, 'Data karyawan tidak ditemukan. Hubungi administrator.');
        }

        if ($user->employee->role !== 'lecturer') {
            abort(403, 'Akses terbatas untuk dosen saja.');
        }

        return $next($request);
    }
}
