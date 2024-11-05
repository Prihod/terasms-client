<?php

namespace TeraSMS\Request;

class StatusViberRequest extends StatusRequest
{
    protected string $uri = 'viber_status';

    public function toArray(): array
    {
        return ['ids' => $this->ids];
    }
}
