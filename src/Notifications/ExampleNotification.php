<?php

namespace DevKandil\NotiFire\Notifications;

use DevKandil\NotiFire\FcmMessage;
use DevKandil\NotiFire\Enums\MessagePriority;
use Illuminate\Notifications\Notification;

class ExampleNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(private string $title, private string $body)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['fcm'];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        return FcmMessage::create($this->title, $this->body)
            ->image('https://example.com/notification-image.jpg')
            ->sound('default')
            ->clickAction('OPEN_ACTIVITY')
            ->icon('notification_icon')
            ->color('#FF5733')
            ->priority(MessagePriority::HIGH)
            ->data([
                'notification_id' => uniqid('notification_'),
                'timestamp' => now()->toIso8601String(),
            ]);
    }
}