<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NonLecturerOnly
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

        // Define administrative roles
        $administrativeRoles = [
            'hr_finance_staff',
            'head_of_hr_finance',
            'fri_vice_dean',
            'head_of_study_program',
            'head_of_research_group'
        ];

        if (!in_array($user->employee->role, $administrativeRoles)) {
            abort(403, 'Akses terbatas untuk staf administrasi saja.');
        }

        return $next($request);
    }
}
