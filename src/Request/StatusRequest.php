<?php

namespace TeraSMS\Request;

class StatusRequest extends Request
{
    protected string $uri = 'getstatus/json';
    protected array $ids = [];

    public function __construct(array $ids = [])
    {
        $this->ids = $ids;
    }

    public function appendMessageId(int $id): self
    {



        $this->ids[] = $id;


        return $this;
    }

    public function setMessageIds(array $ids): self
    {
        $this->ids = $ids;
        return $this;
    }

    public function toArray(): array
    {
        return ['message_ids' => $this->ids];
    }
}
