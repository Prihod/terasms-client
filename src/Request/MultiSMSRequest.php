<?php

namespace TeraSMS\Request;

class MultiSMSRequest extends MultiRequest
{
    protected string $uri = 'msend_json';

    public function toArray(): array
    {
        return [
            'smsPackage' => $this->requestsToArray()
        ];
    }
}
