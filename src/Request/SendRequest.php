<?php

namespace TeraSMS\Request;

abstract class SendRequest extends Request
{
    protected string $uri = 'send/json';
    protected string $type = '';
    protected string $target;
    protected string $sender;
    protected string $message;

    public static function preparePhone(string $phone): int
    {
        return (int)preg_replace('/\D/', '', $phone);
    }

    public function setPhone(string $phone): self
    {
        $this->setTarget(self::preparePhone($phone));
        return $this;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'target' => $this->target,
            'sender' => $this->sender,
            'message' => $this->message,
        ];

        if ($this->getType()) {
            $data['type'] = $this->getType();
        }
        return $data;
    }

    protected function getType(): string
    {
        return $this->type ?: '';
    }

    protected function setTarget(string $target): self
    {
        $this->target = $target;
        return $this;
    }
}
