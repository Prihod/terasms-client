<?php

namespace TeraSMS\Request;

class ViberRequest extends SendRequest
{
    protected ?string $link = null;
    protected ?string $image = null;
    protected ?string $buttonText = null;
    protected ?int $ttl = null;

    public function setTTL(int $ttl): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function setButtonText(string $text): self
    {
        $this->buttonText = $text;
        return $this;
    }

    public function getUri(): string
    {
        if ($this->hasMedia()) {
            return 'send_viber';
        }

        return parent::getUri();
    }


    public function toArray(): array
    {

        $data = parent::toArray();

        if ($this->ttl && $this->ttl >= 60 && $this->ttl <= 86400) {
            $data['ttl'] = $this->ttl;
        }

        if ($this->image) {
            $data['image_url'] = $this->image;
        }
        if ($this->link) {
            $data['button_link'] = $this->link;
        }
        if ($this->buttonText) {
            $data['button_text'] = $this->buttonText;
        }

        return $data;
    }

    protected function getType(): string
    {
        if (!$this->hasMedia()) {
            return 'viber';
        }

        return $this->type;
    }

    protected function hasMedia(): bool
    {
        return $this->image || $this->link || $this->buttonText;
    }
}
