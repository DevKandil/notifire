<?php

namespace DevKandil\NotiFire\Tests\Unit;

use DevKandil\NotiFire\Tests\TestCase;
use DevKandil\NotiFire\Traits\HasFcm;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;

class HasFcmTest extends TestCase
{
    protected TestModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TestModel();
    }

    #[Test]
    public function it_sets_and_gets_fcm_token()
    {
        $token = 'fMYt3W8XSJqTMEIvYR1234:APA91bEH_3kDyFMuXO5awEcbkwqg9LDyZ8QK-9qAw3qsF-4NvUq98Y5X9iJKX2JkpRGLEN_2PXXXPmLTCWtQWYPmL3RKJki_6GVQgHGpXzD8YG9z1EUlZ6LWmjOUCxGrYD8QVnqH1234';
        $this->model->setFcmToken($token);

        $this->assertEquals($token, $this->model->getFcmToken());
    }

    #[Test]
    public function it_returns_fcm_token_for_notification_routing()
    {
        $token = 'fMYt3W8XSJqTMEIvYR1234:APA91bEH_3kDyFMuXO5awEcbkwqg9LDyZ8QK-9qAw3qsF-4NvUq98Y5X9iJKX2JkpRGLEN_2PXXXPmLTCWtQWYPmL3RKJki_6GVQgHGpXzD8YG9z1EUlZ6LWmjOUCxGrYD8QVnqH1234';
        $this->model->setFcmToken($token);

        $this->assertEquals($token, $this->model->routeNotificationForFcm());
    }

    #[Test]
    public function it_returns_null_when_fcm_token_not_set()
    {
        $this->assertNull($this->model->getFcmToken());
        $this->assertNull($this->model->routeNotificationForFcm());
    }
}

// Test model class
class TestModel extends Model
{
    use HasFcm;

    protected $fillable = ['fcm_token'];
}