<?php

namespace TeraSMS\Request;

class MultiViberRequest extends MultiRequest
{
    protected string $uri = 'send_viber_bulk/json';

    public function toArray(): array
    {
        return [
            'messages' => $this->requestsToArray()
        ];
    }
}
