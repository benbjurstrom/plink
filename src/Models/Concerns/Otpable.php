<?php

namespace BenBjurstrom\Plink\Models\Concerns;

use BenBjurstrom\Plink\Models\Otp;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface Otpable extends Authenticatable, MustVerifyEmail
{
    /**
     * @return HasMany<Otp>
     */
    public function otps(): HasMany;

    /**
     * @return void
     */
    public function notify(mixed $instance);
}
