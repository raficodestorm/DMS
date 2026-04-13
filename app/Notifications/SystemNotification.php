<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public $details;

    // এখানে $details একটি Array হিসেবে আসবে
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->details['title'] ?? 'System Alert',
            'message' => $this->details['message'] ?? '',
            'url'     => $this->details['url'] ?? '#',
            'type'    => $this->details['type'] ?? 'info', // নোটিফিকেশন টাইপ (success, danger, etc.)
        ];
    }
}
