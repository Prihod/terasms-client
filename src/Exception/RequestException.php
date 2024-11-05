<?php

namespace TeraSMS\Exception;

use TeraSMS\Request\RequestInterface;
use Exception;
use Throwable;

class RequestException extends Exception
{
    public RequestInterface $request;

    public function __construct(
        RequestInterface $request,
        string $message,
        int $code,
        Throwable $previous
    ) {
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }
}
