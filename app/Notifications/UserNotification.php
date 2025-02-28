<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $type;

    protected $url;

    public function __construct($message, $type, $url)
    {
        $this->message = $message;
        $this->type = $type; // Example: success, error, info
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database']; // Store in database
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'url' => $this->url,
            'time' => now()->format('H:i'), // Time format
        ];
    }
}
