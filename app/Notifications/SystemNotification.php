<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $details;

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function via(object $notifiable): array
    {

        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => $this->details['title'] ?? 'System Alert',
            'message' => $this->details['message'] ?? '',
            'url'     => $this->details['url'] ?? '#',
            'type'    => $this->details['type'] ?? 'info',
        ];
    }

    // এটি রিয়েল-টাইম ব্রডকাস্টিং এর জন্য
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title'   => $this->details['title'] ?? 'System Alert',
            'message' => $this->details['message'] ?? '',
            'url'     => $this->details['url'] ?? '#',
            'type'    => $this->details['type'] ?? 'info',
        ]);
    }
}
