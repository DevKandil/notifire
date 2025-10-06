<?php

namespace DevKandil\NotiFire\Tests\Unit;

use DevKandil\NotiFire\Enums\MessagePriority;
use DevKandil\NotiFire\FcmMessage;
use DevKandil\NotiFire\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FcmMessageTest extends TestCase
{
    protected FcmMessage $message;

    protected function setUp(): void
    {
        parent::setUp();
        $this->message = new FcmMessage('Default Title', 'Default Body');
    }

    #[Test]
    public function it_sets_title()
    {
        // Title is set in constructor, we can only verify it
        $this->assertEquals('Default Title', $this->message->title);
    }

    #[Test]
    public function it_sets_body()
    {
        // Body is set in constructor, we can only verify it
        $this->assertEquals('Default Body', $this->message->body);
    }

    #[Test]
    public function it_sets_image()
    {
        $image = 'test_image.jpg';
        $this->message->image($image);

        $this->assertEquals($image, $this->message->image);
    }

    #[Test]
    public function it_sets_sound()
    {
        $sound = 'test_sound.mp3';
        $this->message->sound($sound);

        $this->assertEquals($sound, $this->message->sound);
    }

    #[Test]
    public function it_sets_click_action()
    {
        $action = 'test_action';
        $this->message->clickAction($action);

        $this->assertEquals($action, $this->message->clickAction);
    }

    #[Test]
    public function it_sets_icon()
    {
        $icon = 'test_icon.png';
        $this->message->icon($icon);

        $this->assertEquals($icon, $this->message->icon);
    }

    #[Test]
    public function it_sets_priority()
    {
        $priority = MessagePriority::HIGH;
        $this->message->priority($priority);

        $this->assertEquals($priority, $this->message->priority);
    }

    #[Test]
    public function it_sets_data()
    {
        $data = ['key' => 'value'];
        $this->message->data($data);

        $this->assertEquals($data, $this->message->data);
    }

    #[Test]
    public function it_builds_message_with_all_attributes()
    {
        $message = FcmMessage::create('Test Title', 'Test Body')
            ->image('test_image.jpg')
            ->sound('test_sound.mp3')
            ->clickAction('test_action')
            ->icon('test_icon.png')
            ->priority(MessagePriority::HIGH)
            ->data(['key' => 'value']);

        $this->assertEquals('Test Title', $message->title);
        $this->assertEquals('Test Body', $message->body);
        $this->assertEquals('test_image.jpg', $message->image);
        $this->assertEquals('test_sound.mp3', $message->sound);
        $this->assertEquals('test_action', $message->clickAction);
        $this->assertEquals('test_icon.png', $message->icon);
        $this->assertEquals(MessagePriority::HIGH, $message->priority);
        $this->assertEquals(['key' => 'value'], $message->data);
    }

    #[Test]
    public function it_sets_single_topic()
    {
        $topic = 'news';
        $this->message->toTopics($topic);

        $this->assertEquals($topic, $this->message->topics);
    }

    #[Test]
    public function it_sets_multiple_topics()
    {
        $topics = ['news', 'updates'];
        $this->message->toTopics($topics);

        $this->assertEquals($topics, $this->message->topics);
    }

    #[Test]
    public function it_builds_message_with_topic()
    {
        $message = FcmMessage::create('Topic News', 'Breaking news!')
            ->toTopics('news')
            ->priority(MessagePriority::HIGH);

        $this->assertEquals('Topic News', $message->title);
        $this->assertEquals('Breaking news!', $message->body);
        $this->assertEquals('news', $message->topics);
        $this->assertEquals(MessagePriority::HIGH, $message->priority);
    }
}