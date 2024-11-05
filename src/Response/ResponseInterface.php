<?php

namespace TeraSMS\Response;

use TeraSMS\Response\Entry\EntryInterface;

interface ResponseInterface
{
    public static function fromJson(string $json): self;

    public function isSuccess(): bool;

    public function getStatus(): ?int;

    public function getData(): array;

    public function getEntries(): array;

    public function getFirstEntry(): ?EntryInterface;

    public function getError(): string;

    public function toArray(): array;
}
