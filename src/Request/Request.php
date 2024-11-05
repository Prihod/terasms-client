<?php

namespace TeraSMS\Request;

abstract class Request implements RequestInterface
{
    protected string $uri = 'json';

    public function getUri(): string
    {
        return $this->uri;
    }

    public function toArray(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return (string)json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
