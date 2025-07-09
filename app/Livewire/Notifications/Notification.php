<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Notification extends Component
{
    use WithPagination;

    public $filter = 'unread';

    public function filter($type)
    {
        $this->filter = $type;
        $this->resetPage();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->resetPage();
    }

    public function render()
    {
        $query = Auth::user()->notifications()->latest();
        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }
        $notifications = $query->paginate(10);

        return view('livewire.notifications.notification', [
            'notifications' => $notifications,
        ]);
    }
}
