<?php

namespace DevKandil\NotiFire\Channels;

use DevKandil\NotiFire\FcmService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            Log::warning('Notification does not have toFcm method', [
                'notification' => get_class($notification)
            ]);
            return;
        }

        $fcmToken = $notifiable->routeNotificationFor('fcm', $notification);

        if (empty($fcmToken)) {
            Log::warning('No FCM token found for notifiable', [
                'notifiable_id' => $notifiable->getKey(),
                'notifiable_type' => get_class($notifiable)
            ]);
            return;
        }

        $message = $notification->toFcm($notifiable);

        try {
            if (is_array($message)) {
                $this->fcmService->fromRaw($message)->send();
            } else {
                $this->fcmService
                    ->withTitle($message->title)
                    ->withBody($message->body)
                    ->withAdditionalData($message->data ?? [])
                    ->withPriority($message->priority ?? null)
                    ->withSound($message->sound ?? null)
                    ->withImage($message->image ?? null)
                    ->withIcon($message->icon ?? null)
                    ->withClickAction($message->clickAction ?? null)
                    ->sendNotification($fcmToken);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send FCM notification through channel', [
                'error' => $e->getMessage(),
                'notifiable_id' => $notifiable->getKey(),
                'notifiable_type' => get_class($notifiable)
            ]);
        }
    }
}