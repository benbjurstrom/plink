<?php

namespace BenBjurstrom\Plink\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BenBjurstrom\Plink\Plink
 */
class Plink extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \BenBjurstrom\Plink\Plink::class;
    }
}
