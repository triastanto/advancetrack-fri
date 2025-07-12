<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Notification extends Component
{
    use WithPagination;

    public $currentFilter = 'unread';
    public $selectedNotifications = [];
    public $selectAll = false;

    protected $queryString = ['currentFilter'];

    public function mount()
    {
        $this->currentFilter = request()->get('currentFilter', 'unread');
        
        // Debug logging
        \Illuminate\Support\Facades\Log::info('Notification component mounted', [
            'filter' => $this->currentFilter,
            'user_id' => Auth::id()
        ]);
    }

    public function filter($type)
    {
        $this->currentFilter = $type;
        $this->resetPage();
        $this->reset(['selectedNotifications', 'selectAll']);
        
        // Debug logging
        \Illuminate\Support\Facades\Log::info('Notification filter changed', [
            'filter' => $type,
            'user_id' => Auth::id(),
            'timestamp' => now()
        ]);
    }



    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification-read', ['id' => $notificationId]);
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->reset(['selectedNotifications', 'selectAll']);
        $this->dispatch('notifications-updated');
    }

    public function deleteNotification($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->delete();
            $this->dispatch('notification-deleted', ['id' => $notificationId]);
        }
    }

    public function deleteSelected()
    {
        if (!empty($this->selectedNotifications)) {
            Auth::user()->notifications()
                ->whereIn('id', $this->selectedNotifications)
                ->delete();
            
            $this->reset(['selectedNotifications', 'selectAll']);
            $this->dispatch('notifications-updated');
        }
    }

    public function markSelectedAsRead()
    {
        if (!empty($this->selectedNotifications)) {
            Auth::user()->notifications()
                ->whereIn('id', $this->selectedNotifications)
                ->update(['read_at' => now()]);
            
            $this->reset(['selectedNotifications', 'selectAll']);
            $this->dispatch('notifications-updated');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedNotifications = $this->getNotifications()->pluck('id')->toArray();
        } else {
            $this->selectedNotifications = [];
        }
    }

    public function updatedSelectedNotifications()
    {
        $this->selectAll = false;
    }

    public function getUnreadCount()
    {
        return Auth::user()->unreadNotifications()->count();
    }

    public function getNotifications()
    {
        $query = Auth::user()->notifications()->latest();

        // Apply filter
        if ($this->currentFilter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->currentFilter === 'read') {
            $query->whereNotNull('read_at');
        }
        // 'all' filter shows all notifications (no additional where clause)

        // Debug logging
        \Illuminate\Support\Facades\Log::info('Getting notifications with filter', [
            'filter' => $this->currentFilter,
            'user_id' => Auth::id(),
            'count' => $query->count()
        ]);

        return $query->paginate(15);
    }

    public function render()
    {
        $notifications = $this->getNotifications();
        $unreadCount = $this->getUnreadCount();

        return view('livewire.notifications.notification', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
