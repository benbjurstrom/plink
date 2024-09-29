<?php

namespace BenBjurstrom\Plink\Enums;

enum PlinkStatus: int
{
    case ACTIVE = 0;
    case SUPERSEDED = 1;
    case EXPIRED = 2;
    case USED = 3;
    case INVALID = 4;
    case SESSION = 5;

    public function errorMessage(): string
    {
        return match ($this) {
            self::ACTIVE => 'The link is still active.',
            self::SUPERSEDED => 'The link you clicked was superseded by subsequent request. Please use the most recent link.',
            self::EXPIRED => 'The link you clicked has expired. Please request a new link.',
            self::USED => 'The link you clicked has already been used. Please request a new link.',
            self::INVALID => 'The link you clicked is invalid. Please request a new link.',
            self::SESSION => 'The link you clicked is tied to a different browser session. Please open the link in the same browser where it was requested.',
        };
    }
}
