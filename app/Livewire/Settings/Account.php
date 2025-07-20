<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class Account extends Component
{
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    public $new_email;
    public $email_password;
    public $delete_password;
    public $delete_confirm = false;
    public $activeTab = 'change-password';

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = bcrypt($this->new_password);
        $user->save();

        session()->flash('success', 'Password changed successfully.');
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    public function changeEmail()
    {
        $this->validate([
            'new_email' => ['required', 'email', 'unique:users,email'],
            'email_password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        $user->pending_email = $this->new_email;
        $user->save();

        $user->sendPendingEmailVerificationNotification();

        session()->flash('email_success', 'A verification link has been sent to your new email address. Please verify to complete the change.');
        $this->reset(['new_email', 'email_password']);
    }

    public function confirmDeleteAccount()
    {
        $this->delete_confirm = true;
    }

    public function cancelDeleteAccount()
    {
        $this->delete_confirm = false;
        $this->reset('delete_password');
    }

    public function deleteAccount()
    {
        $this->validate([
            'delete_password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        session()->flash('delete_success', 'Your account has been deleted.');
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.settings.account');
    }
}
