<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\ResearchLab;
use App\Models\ResearchGroup;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function create()
    {
        $researchGroups = ResearchGroup::with('researchLabs')->get();
        
        return view('auth.register', compact('researchGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // User fields
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Employee fields
            'nidn' => ['required', 'string', 'max:50', 'unique:employees'],
            'position' => ['required', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'functional_position' => ['nullable', 'string', 'max:255'],
            'origin_address' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'research_lab_id' => ['nullable', 'exists:research_labs,id'],
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create employee with lecturer role as default
        $employee = Employee::create([
            'user_id' => $user->id,
            'nidn' => $request->nidn,
            'position' => $request->position,
            'role' => 'lecturer', // Default role
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'functional_position' => $request->functional_position,
            'origin_address' => $request->origin_address,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'research_lab_id' => $request->research_lab_id,
            'is_lab_head' => false, // Default to false
            'is_approved' => false, // Explicitly set to false
        ]);

        // Send email verification
        event(new Registered($user));

        // Redirect to login with a success message
        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan cek email Anda untuk verifikasi.');
    }
} 