<?php

namespace DevKandil\NotiFire\Tests\Unit;

use DevKandil\NotiFire\Enums\MessagePriority;
use DevKandil\NotiFire\FcmService;
use DevKandil\NotiFire\Tests\TestCase;
use Google_Client;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class FcmServiceTest extends TestCase
{
    protected FcmService $fcmService;
    protected string $testToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fcmService = new FcmService();
        $this->testToken = config('fcm.test_token');
    }

    #[Test]
    public function it_can_build_fcm_service()
    {
        $service = FcmService::build();
        $this->assertInstanceOf(FcmService::class, $service);
    }

    #[Test]
    public function it_can_set_notification_attributes()
    {
        $service = FcmService::build()
            ->withTitle('Test Title')
            ->withBody('Test Body')
            ->withClickAction('test_action')
            ->withImage('test_image.jpg')
            ->withIcon('test_icon.png')
            ->withSound('test_sound.mp3')
            ->withPriority(MessagePriority::HIGH)
            ->withAdditionalData(['key' => 'value']);

        $this->assertInstanceOf(FcmService::class, $service);
    }

    #[Test]
    public function it_fails_to_send_notification_with_empty_token()
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Empty FCM token provided');

        $result = $this->fcmService->sendNotification('');
        $this->assertFalse($result);
    }

    #[Test]
    public function it_sends_notification_successfully()
    {
        // Mock Google_Client
        $googleClient = Mockery::mock(Google_Client::class);
        $googleClient->shouldReceive('setAuthConfig')->once();
        $googleClient->shouldReceive('addScope')->once();
        $googleClient->shouldReceive('refreshTokenWithAssertion')->once();
        $googleClient->shouldReceive('getAccessToken')
            ->once()
            ->andReturn(['access_token' => 'test-access-token']);

        $this->app->instance(Google_Client::class, $googleClient);

        // Mock successful API response
        $this->mockSuccessfulApiCall();

        $result = $this->fcmService
            ->withTitle('Test Title')
            ->withBody('Test Body')
            ->sendNotification($this->testToken);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_handles_api_error()
    {
        // Mock Google_Client
        $googleClient = Mockery::mock(Google_Client::class);
        $googleClient->shouldReceive('setAuthConfig')->once();
        $googleClient->shouldReceive('addScope')->once();
        $googleClient->shouldReceive('refreshTokenWithAssertion')->once();
        $googleClient->shouldReceive('getAccessToken')
            ->once()
            ->andReturn(['access_token' => 'test-access-token']);

        $this->app->instance(Google_Client::class, $googleClient);

        // Mock failed API response
        $this->mockFailedApiCall();

        Log::shouldReceive('error')
            ->once()
            ->with('Failed to send FCM notification', Mockery::any());

        $result = $this->fcmService
            ->withTitle('Test Title')
            ->withBody('Test Body')
            ->sendNotification($this->testToken);

        $this->assertFalse($result);
    }

    protected function mockSuccessfulApiCall()
    {
        $successResponse = json_encode(['name' => 'test-message-id']);
        $this->mockCurlExec($successResponse);
    }

    protected function mockFailedApiCall()
    {
        $errorResponse = json_encode(['error' => 'Test error message']);
        $this->mockCurlExec($errorResponse);
    }

    protected function mockCurlExec($response)
    {
        // Override curl_exec function
        eval('namespace DevKandil\\NotiFire; function curl_exec($ch) { return \'' . $response . '\';}');
        eval('namespace DevKandil\\NotiFire; function curl_getinfo($ch, $opt = null) { return 200; }');
        eval('namespace DevKandil\\NotiFire; function curl_error($ch) { return ""; }');
    }

    #[Test]
    public function it_can_set_raw_message_and_send()
    {
        // Mock Google_Client
        $googleClient = Mockery::mock(Google_Client::class);
        $googleClient->shouldReceive('setAuthConfig')->once();
        $googleClient->shouldReceive('addScope')->once();
        $googleClient->shouldReceive('refreshTokenWithAssertion')->once();
        $googleClient->shouldReceive('getAccessToken')
            ->once()
            ->andReturn(['access_token' => 'test-access-token']);

        $this->app->instance(Google_Client::class, $googleClient);

        // Mock successful API response
        $this->mockSuccessfulApiCall();

        $rawMessage = [
            'message' => [
                'notification' => [
                    'title' => 'Raw Title',
                    'body' => 'Raw Body',
                ],
                'token' => $this->testToken
            ]
        ];

        $service = $this->fcmService->fromRaw($rawMessage);
        
        // Verify fromRaw returns the service instance
        $this->assertInstanceOf(FcmService::class, $service);
        
        // Verify send method works with the raw message
        $response = $service->send();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('name', $response);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}