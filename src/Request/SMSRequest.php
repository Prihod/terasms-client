<?php

namespace TeraSMS\Request;

class SMSRequest extends SendRequest
{
    protected string $type = 'sms';
    protected ?int $id = null;

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        if ($this->id !== null) {
            $data['sms_id'] = $this->id;
        }

        return $data;
    }
}
