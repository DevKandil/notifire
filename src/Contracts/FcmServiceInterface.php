<?php

namespace DevKandil\NotiFire\Contracts;

use DevKandil\NotiFire\FcmMessage;
use DevKandil\NotiFire\Enums\MessagePriority;

interface FcmServiceInterface
{
    /**
     * Set the notification title.
     *
     * @param string $title
     * @return self
     */
    public function withTitle(string $title): self;

    /**
     * Set the notification body.
     *
     * @param string $body
     * @return self
     */
    public function withBody(string $body): self;

    /**
     * Set the notification image.
     *
     * @param string $image
     * @return self
     */
    public function withImage(string $image): self;

    /**
     * Set the notification sound.
     *
     * @param string $sound
     * @return self
     */
    public function withSound(string $sound): self;

    /**
     * Set the notification click action.
     *
     * @param string $action
     * @return self
     */
    public function withClickAction(string $action): self;

    /**
     * Set the notification icon.
     *
     * @param string $icon
     * @return self
     */
    public function withIcon(string $icon): self;
    
    /**
     * Set the notification color.
     *
     * @param string $color
     * @return self
     */
    public function withColor(string $color): self;

    /**
     * Set the notification priority.
     *
     * @param MessagePriority $priority
     * @return self
     */
    public function withPriority(MessagePriority $priority): self;

    /**
     * Set additional data for the notification.
     *
     * @param array $data
     * @return self
     */
    public function withAdditionalData(array $data): self;

    /**
     * Send a notification to a specific FCM token.
     *
     * @param string $token
     * @return bool
     */
    public function sendNotification(string $token): bool;

    /**
     * Set a raw FCM message to be sent later.
     *
     * @param array $message
     * @return self
     */
    public function fromRaw(array $message): self;

    /**
     * Send a notification to one or multiple topics.
     *
     * @param string|array $topics Single topic (string) or multiple topics (array)
     * @return bool
     */
    public function sendToTopics(string|array $topics): bool;
}