<?php

namespace BenBjurstrom\Plink\Models\Concerns;

use BenBjurstrom\Plink\Models\Plink;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasPlinks
{
    /**
     * @return HasMany<Plink>
     */
    public function plinks(): HasMany
    {
        return $this->hasMany(Plink::class);
    }
}
