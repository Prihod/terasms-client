<?php

namespace TeraSMS\Request;

interface RequestInterface
{
    public function getUri(): string;

    public function toArray(): array;
}
