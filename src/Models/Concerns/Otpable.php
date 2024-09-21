<?php

namespace BenBjurstrom\Otpz\Models\Concerns;

use BenBjurstrom\Otpz\Models\Otp;
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
     * @param mixed $instance
     * @return void
     */
    public function notify(mixed $instance);
}
