<?php

namespace BenBjurstrom\Otpz\Models\Concerns;

use BenBjurstrom\Otpz\Models\Otp;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOtps
{
    /**
     * @return HasMany<Otp>
     */
    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }
}
