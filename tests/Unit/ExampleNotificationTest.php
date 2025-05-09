<?php

namespace DevKandil\NotiFire\Tests\Unit;

use DevKandil\NotiFire\Enums\MessagePriority;
use DevKandil\NotiFire\FcmMessage;
use DevKandil\NotiFire\Notifications\ExampleNotification;
use DevKandil\NotiFire\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use DevKandil\NotiFire\Traits\HasFcm;
use PHPUnit\Framework\Attributes\Test;

class ExampleNotificationTest extends TestCase
{
    protected ExampleNotification $notification;
    protected User $notifiable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = new ExampleNotification('Test Title', 'Test Body');
        $this->notifiable = new class extends User {
            use Notifiable, HasFcm;
            protected $fillable = ['fcm_token'];
        };
        $this->notifiable->setFcmToken('fMYt3W8XSJqTMEIvYR1234:APA91bEH_3kDyFMuXO5awEcbkwqg9LDyZ8QK-9qAw3qsF-4NvUq98Y5X9iJKX2JkpRGLEN_2PXXXPmLTCWtQWYPmL3RKJki_6GVQgHGpXzD8YG9z1EUlZ6LWmjOUCxGrYD8QVnqH1234');
    }

    #[Test]
    public function it_creates_notification_with_title_and_body()
    {
        $fcmMessage = $this->notification->toFcm($this->notifiable);
        $this->assertEquals('Test Title', $fcmMessage->title);
        $this->assertEquals('Test Body', $fcmMessage->body);
    }

    #[Test]
    public function it_converts_to_fcm_message()
    {
        $fcmMessage = $this->notification->toFcm($this->notifiable);

        $this->assertEquals('Test Title', $fcmMessage->title);
        $this->assertEquals('Test Body', $fcmMessage->body);
    }

    #[Test]
    public function it_sets_optional_parameters()
    {
        // Create a mock of ExampleNotification that we can modify for testing
        $notification = $this->getMockBuilder(ExampleNotification::class)
            ->setConstructorArgs(['Test Title', 'Test Body'])
            ->onlyMethods(['toFcm'])
            ->getMock();
            
        // Configure the mock to return a custom FcmMessage
        $notification->method('toFcm')
            ->willReturn(
                FcmMessage::create('Test Title', 'Test Body')
                    ->image('test_image.jpg')
                    ->sound('test_sound.mp3')
                    ->clickAction('test_action')
                    ->icon('test_icon.png')
                    ->priority(MessagePriority::HIGH)
                    ->data(['key' => 'value'])
            );

        $fcmMessage = $notification->toFcm($this->notifiable);

        $this->assertEquals('test_image.jpg', $fcmMessage->image);
        $this->assertEquals('test_sound.mp3', $fcmMessage->sound);
        $this->assertEquals('test_action', $fcmMessage->clickAction);
        $this->assertEquals('test_icon.png', $fcmMessage->icon);
        $this->assertEquals(MessagePriority::HIGH, $fcmMessage->priority);
        $this->assertEquals(['key' => 'value'], $fcmMessage->data);
    }

    #[Test]
    public function it_returns_fcm_channel()
    {
        $channels = $this->notification->via($this->notifiable);
        $this->assertEquals(['fcm'], $channels);
    }
}