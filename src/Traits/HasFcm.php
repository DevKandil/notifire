<?php

namespace DevKandil\NotiFire\Traits;

use DevKandil\NotiFire\Facades\Fcm;

trait HasFcm
{
    /**
     * Get the FCM token for the model.
     *
     * @return string|null
     */
    public function getFcmToken()
    {
        return $this->fcm_token;
    }

    /**
     * Update the FCM token for the model.
     *
     * @param string $token
     * @return bool
     */
    public function updateFcmToken(string $token)
    {
        return $this->update([
            'fcm_token' => $token,
        ]);
    }

    /**
     * Send an FCM notification to this model.
     *
     * @param string $title
     * @param string $body
     * @param array $options
     * @return bool
     */
    public function sendFcmNotification(string $title, string $body, array $options = [])
    {
        if (!$this->fcm_token) {
            return false;
        }

        $notification = Fcm::withTitle($title)
            ->withBody($body);

        if (isset($options['image'])) {
            $notification->withImage($options['image']);
        }

        if (isset($options['sound'])) {
            $notification->withSound($options['sound']);
        }

        if (isset($options['click_action'])) {
            $notification->withClickAction($options['click_action']);
        }

        if (isset($options['icon'])) {
            $notification->withIcon($options['icon']);
        }
        
        if (isset($options['color'])) {
            $notification->withColor($options['color']);
        }

        if (isset($options['data'])) {
            $notification->withAdditionalData($options['data']);
        }

        if (isset($options['priority'])) {
            $notification->withPriority($options['priority']);
        }

        return $notification->sendNotification($this->fcm_token);
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param mixed $notification
     * @return string|null
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->fcm_token;
    }
}