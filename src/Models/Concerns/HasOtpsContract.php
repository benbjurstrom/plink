<?php

namespace BenBjurstrom\Otpz\Models\Concerns;

use BenBjurstrom\Otpz\Models\Otp;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasOtpsContract extends Authenticatable, MustVerifyEmail
{
    /**
     * @return HasMany<Otp>
     */
    public function otps(): HasMany;

    public function notify(mixed $instance): void;
}
