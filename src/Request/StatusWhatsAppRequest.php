<?php

namespace TeraSMS\Request;

class StatusWhatsAppRequest extends Request
{
    protected string $uri = 'getMessageAnswers/json';
    protected int $id;

    public function setMessageId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function toArray(): array
    {
        return ['message_id' => $this->id];
    }
}
