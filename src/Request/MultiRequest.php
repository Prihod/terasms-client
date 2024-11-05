<?php

namespace TeraSMS\Request;

abstract class MultiRequest extends Request
{
    protected array $requests = [];

    public function __construct(array $requests = [])
    {
        $this->requests = $requests;
    }


    public function append(Request $request): self
    {
        $this->requests[] = $request;
        return $this;
    }

    protected function requestsToArray(): array
    {
        $data = [];
        foreach ($this->requests as $request) {
            $data[] = $request->toArray();
        }
        return $data;
    }
}
