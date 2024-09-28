<?php

namespace BenBjurstrom\Plink\Enums;

enum PlinkStatus: int
{
    case ACTIVE = 0;
    case SUPERSEDED = 1;
    case EXPIRED = 2;
    case USED = 3;

    public function errorMessage(): string
    {
        return match ($this) {
            self::ACTIVE => 'The link is still active.',
            self::SUPERSEDED => 'The link been superseded. Please request a new link.',
            self::EXPIRED => 'The link has expired. Please request a new link.',
            self::USED => 'The link has already been used. Please request a new link.',
        };
    }
}
