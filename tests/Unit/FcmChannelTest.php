<?php

namespace DevKandil\NotiFire\Tests\Unit;

use DevKandil\NotiFire\Channels\FcmChannel;
use DevKandil\NotiFire\FcmMessage;
use DevKandil\NotiFire\FcmService;
use DevKandil\NotiFire\Tests\TestCase;
use Illuminate\Notifications\Notification;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class FcmChannelTest extends TestCase
{
    protected FcmChannel $channel;
    protected $fcmService;
    protected $notifiable;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fcmService = Mockery::mock(FcmService::class, []);
        $this->channel = new FcmChannel($this->fcmService);
        
        // Create a mock notifiable
        $this->notifiable = new class {
            public function routeNotificationForFcm()
            {
                return 'fMYt3W8XSJqTMEIvYR1234:APA91bEH_3kDyFMuXO5awEcbkwqg9LDyZ8QK-9qAw3qsF-4NvUq98Y5X9iJKX2JkpRGLEN_2PXXXPmLTCWtQWYPmL3RKJki_6GVQgHGpXzD8YG9z1EUlZ6LWmjOUCxGrYD8QVnqH1234';
            }
        };

        // Create a mock notification
        $this->notification = new class extends Notification {
            public function toFcm($notifiable)
            {
                return new FcmMessage('Test Title', 'Test Body');
            }
        };
    }

    #[Test]
    public function it_sends_notification_through_fcm_channel()
    {
        $this->fcmService->shouldReceive('sendNotification')
            ->once()
            ->with('test-fcm-token')
            ->andReturn(true);

        $this->channel->send($this->notifiable, $this->notification);
    }

    #[Test]
    public function it_handles_missing_fcm_token()
    {
        $notifiable = new class {
            public function routeNotificationForFcm()
            {
                return null;
            }
        };

        $this->fcmService->shouldNotReceive('sendNotification');

        $this->channel->send($notifiable, $this->notification);
    }

    #[Test]
    public function it_handles_invalid_notification_format()
    {
        $invalidNotification = new class extends Notification {
            public function toFcm($notifiable)
            {
                return 'invalid-format';
            }
        };

        $this->fcmService->shouldNotReceive('sendNotification');

        $this->channel->send($this->notifiable, $invalidNotification);
    }
    
    #[Test]
    public function it_sends_raw_message_format()
    {
        $rawNotification = new class extends Notification {
            public function toFcm($notifiable)
            {
                return [
                    'message' => [
                        'notification' => [
                            'title' => 'Raw Title',
                            'body' => 'Raw Body',
                        ],
                        'token' => 'test-fcm-token'
                    ]
                ];
            }
        };

        $this->fcmService->shouldReceive('fromRaw')
            ->once()
            ->with([
                'message' => [
                    'notification' => [
                        'title' => 'Raw Title',
                        'body' => 'Raw Body',
                    ],
                    'token' => 'test-fcm-token'
                ]
            ])
            ->andReturnSelf();
            
        $this->fcmService->shouldReceive('send')
            ->once()
            ->andReturn(json_decode(json_encode(['name' => 'test-message-id']), true));

        $this->channel->send($this->notifiable, $rawNotification);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}