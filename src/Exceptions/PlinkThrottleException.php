<?php

namespace BenBjurstrom\Plink\Exceptions;

use Exception;

class PlinkThrottleException extends Exception
{
    public function __construct(string|int $minutes, string|int $seconds)
    {
        $message = "Too many links requested. Please wait {$minutes} minutes and {$seconds} seconds before trying again.";
        parent::__construct($message);
    }
}
