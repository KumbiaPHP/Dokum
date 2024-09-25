<?php

namespace Dokum\models\Exceptions;

class InvalidContentException extends \Exception
{
    public function __construct($message = "Invalid content", $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
