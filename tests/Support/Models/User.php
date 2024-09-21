<?php

namespace BenBjurstrom\Otpz\Tests\Support\Models;

use BenBjurstrom\Otpz\Models\Concerns\HasOtps;
use BenBjurstrom\Otpz\Models\Concerns\Otpable;
use BenBjurstrom\Otpz\Tests\Support\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends \Illuminate\Foundation\Auth\User implements Otpable
{
    use HasFactory;
    use HasOtps;

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
