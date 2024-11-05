<?php

namespace TeraSMS\Response\Entry;

interface EntryInterface
{
    public function isSuccess(): bool;

    public function getStatus(): ?int;

    public function getData(): array;

    public function getError(): string;

    public function toArray(): array;
}
