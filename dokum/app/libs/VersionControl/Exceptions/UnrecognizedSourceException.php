<?php

namespace Dokum\libs\VersionControl\Exceptions;


class UnrecognizedSourceException extends \Exception
{
    public function __construct(string $url)
    {
        parent::__construct("Unrecognized source: {$url}. Only GitHub and GitLab are supported.");
    }
}
