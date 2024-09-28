<?php

namespace BenBjurstrom\Plink\Exceptions;

use Exception;

class OtpAttemptsException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
