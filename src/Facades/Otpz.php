<?php

namespace BenBjurstrom\Otpz\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BenBjurstrom\Otpz\Otpz
 */
class Otpz extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \BenBjurstrom\Otpz\Otpz::class;
    }
}
