<?php

namespace TeraSMS\Request;

class VoiceOTPRequest extends Request
{
    protected string $uri = 'send/json';
    protected string $type = 'callpass';
    protected ?string $code = null;
    protected string $target;
    protected string $sender;

    public static function generateCode(int $length = 4, string $type = 'numeric'): string
    {
        switch ($type) {
            case 'numeric':
                $characters = '0123456789';
                break;
            case 'alpha':
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alphanumeric':
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default:
                throw new \InvalidArgumentException(
                    "Invalid type specified. Use 'numeric', 'alpha', or 'alphanumeric'."
                );
        }

        $code = '';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->target = $phone;
        return $this;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function toArray(): array
    {
        if ($this->code === null) {
            $this->code = self::generateCode();
        }

        return [
            'type' => $this->type,
            'message' => $this->code,
            'target' => $this->target,
            'sender' => $this->sender,
        ];
    }
}
