<?php

namespace DevKandil\NotiFire\Exceptions;

use Exception;

class FcmRequestException extends Exception
{
    protected array $responseData;

    public function __construct(string $message, array $responseData = [], int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->responseData = $responseData;
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }
}