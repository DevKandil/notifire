<?php

namespace DevKandil\NotiFire;

use DevKandil\NotiFire\Enums\MessagePriority;

class FcmMessage
{
    public string $title;
    public string $body;
    public ?string $clickAction = null;
    public ?string $image = null;
    public ?string $icon = null;
    public ?string $color = null;
    public ?string $sound = null;
    public ?array $data = [];
    public MessagePriority $priority;

    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
        $this->priority = MessagePriority::NORMAL;
    }

    public static function create(string $title, string $body): static
    {
        return new static($title, $body);
    }

    public function clickAction(string $action): static
    {
        $this->clickAction = $action;
        return $this;
    }

    public function image(string $url): static
    {
        $this->image = $url;
        return $this;
    }

    public function icon(string $url): static
    {
        $this->icon = $url;
        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function sound(string $sound): static
    {
        $this->sound = $sound;
        return $this;
    }

    public function data(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function priority(MessagePriority $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    public function highPriority(): static
    {
        $this->priority = MessagePriority::HIGH;
        return $this;
    }
}