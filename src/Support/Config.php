<?php

namespace BenBjurstrom\Plink\Support;

use BenBjurstrom\Plink\Exceptions\InvalidAuthenticatableModel;
use BenBjurstrom\Plink\Models\Concerns\Otpable;
use Illuminate\Database\Eloquent\Model;

class Config
{
    /**
     * @throws InvalidAuthenticatableModel
     */
    public static function getAuthenticatableModel(): string
    {
        $authenticatableModel = config('plink.models.authenticatable');

        if (! is_a($authenticatableModel, Otpable::class, true)) {
            throw InvalidAuthenticatableModel::missingInterface($authenticatableModel, Otpable::class);
        }

        if (! is_subclass_of($authenticatableModel, Model::class, true)) {
            throw InvalidAuthenticatableModel::notExtendingModel($authenticatableModel);
        }

        if (! is_string($authenticatableModel)) {
            throw new \Exception('Authenticatable model must be a string');
        }

        return $authenticatableModel;
    }
}
