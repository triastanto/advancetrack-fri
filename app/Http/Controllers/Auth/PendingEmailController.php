<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class PendingEmailController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! $user->pending_email || ! hash_equals($hash, sha1($user->pending_email))) {
            abort(403, 'Invalid or expired verification link.');
        }

        $user->email = $user->pending_email;
        $user->pending_email = null;
        $user->email_verified_at = now();
        $user->save();

        // Optionally, log the user in
        Auth::login($user);

        return redirect()->route('settings.account')->with('success', 'Your email address has been updated and verified.');
    }
} 