<?php

namespace Dokum\libs\ArchiveExtractor;

use Exception;

class ArchiveExtractionException extends Exception
{
    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        parent::__construct("Archive extraction failed: " . $message, $code, $previous);
    }
}
