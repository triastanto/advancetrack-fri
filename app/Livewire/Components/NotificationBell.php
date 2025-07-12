<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $unreadCount = 0;

    protected $listeners = [
        'notifications-updated' => 'updateUnreadCount',
        'notification-read' => 'updateUnreadCount',
        'notification-deleted' => 'updateUnreadCount',
    ];

    public function mount()
    {
        $this->updateUnreadCount();
    }

    public function updateUnreadCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Auth::user()->unreadNotifications()->count();
        }
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
} 