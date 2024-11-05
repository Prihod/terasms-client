<?php

namespace TeraSMS\Response;

use TeraSMS\Response\Entry\Entry;
use TeraSMS\Response\Entry\EntryInterface;

class Response implements ResponseInterface
{
    const E_INVALID_JSON = 'Invalid json';
    const E_UNKNOWN_ERROR = 'Unknown error';
    const E_BAD_RESPONSE = 'Bad response';

    protected array $data = [];
    protected array $entries = [];
    protected ?int $status = null;
    protected string $error = '';


    public static function fromJson(string $json): self
    {
        if (!$json) {
            return new Response(null, [], self::E_BAD_RESPONSE);
        }

        $responseData = json_decode($json, true);
        if (!$responseData) {
            return new Response(null, [], self::E_INVALID_JSON);
        }

        $keys = ['statuses', 'messages', 'message_infos', 'src_message'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $responseData)) {
                return new Response($responseData['status'] ?? null, $responseData[$key]);
            }
        }
        return new Response($responseData['status'] ?? null, $responseData);
    }


    public function __construct(?int $status, array $data, string $error = '')
    {
        $this->status = $status;
        $this->data = $data;
        $this->error = $error;

        foreach ($this->data as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $this->entries[] = Entry::fromArray($entry);
        }
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

    public function getData(): array
    {
        return $this->data;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getFirstEntry(): ?EntryInterface
    {
        return $this->entries ? $this->entries[0] : null;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'data' => $this->getData(),
            'status' => $this->getStatus(),
            'error' => $this->getError(),
        ];
    }

    public function __toString()
    {
        return (string)json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
