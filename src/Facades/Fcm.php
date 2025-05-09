<?php

namespace DevKandil\NotiFire\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \DevKandil\NotiFire\FcmService withTitle(string $title)
 * @method static \DevKandil\NotiFire\FcmService withBody(string $body)
 * @method static \DevKandil\NotiFire\FcmService withClickAction(string $clickAction)
 * @method static \DevKandil\NotiFire\FcmService withImage(string $image)
 * @method static \DevKandil\NotiFire\FcmService withIcon(string $icon)
 * @method static \DevKandil\NotiFire\FcmService withColor(string $color)
 * @method static \DevKandil\NotiFire\FcmService withSound(string $sound)
 * @method static \DevKandil\NotiFire\FcmService withPriority(\DevKandil\NotiFire\Enums\MessagePriority $priority)
 * @method static \DevKandil\NotiFire\FcmService withAdditionalData(array $additionalData)
 * @method static \DevKandil\NotiFire\FcmService withAuthenticationKey(string $authenticationKey)
 * @method static \DevKandil\NotiFire\FcmService fromArray(array $fromArray)
 * @method static \DevKandil\NotiFire\FcmService fromRaw(array $fromRaw)
 * @method static void sendNotification(array|string $tokens)
 * @method static array send()
 */
class Fcm extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'fcm';
    }
}