<?php

namespace TeraSMS\Response\Entry;

class Entry implements EntryInterface
{
    const E_INVALID_ENTRY = 'Invalid entry data';
    protected array $data;
    protected ?int $status = null;
    protected string $error = '';

    public function __construct(?int $status, array $data, string $error = '')
    {
        $this->status = $status;
        $this->data = $data;
        $this->error = $error;
    }

    public static function fromArray(array $arr): self
    {
        if (!$arr) {
            return new Entry(null, [], self::E_INVALID_ENTRY);
        }

        return new Entry($arr['status'] ?? 0, $arr, $arr['error'] ?? '');
    }

    public function isSuccess(): bool
    {
        $successStatus = [0, 1, 12];
        if (
            !empty($this->error)
            || $this->status < 0
            || !in_array($this->status, $successStatus)
        ) {
            return false;
        }

        return true;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'status' => $this->getStatus(),
            'data' => $this->getData(),
            'error' => $this->getError(),
        ];
    }

    public function __toString()
    {
        return (string)json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
