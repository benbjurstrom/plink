<?php

namespace BenBjurstrom\Plink\Models\Concerns;

use BenBjurstrom\Plink\Models\Plink;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface Plinkable extends Authenticatable, MustVerifyEmail
{
    /**
     * @return HasMany<Plink>
     */
    public function plinks(): HasMany;

    /**
     * @return void
     */
    public function notify(mixed $instance);
}
