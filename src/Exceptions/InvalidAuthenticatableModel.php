<?php

namespace BenBjurstrom\Otpz\Exceptions;

use Exception;

final class InvalidAuthenticatableModel extends Exception
{
    public static function missingInterface(string $modelClass, string $interfaceFqcn): self
    {
        return new static("The model `{$modelClass}` does not use the `{$interfaceFqcn}` interface.");
    }

    public static function notExtendingModel(mixed $authenticatableModel)
    {
        return new static("The model `{$authenticatableModel}` does not extend `Illuminate\Database\Eloquent\Model`.");
    }
}
