<?php

namespace BenBjurstrom\Plink\Tests\Support\Models;

use BenBjurstrom\Plink\Models\Concerns\HasPlinks;
use BenBjurstrom\Plink\Models\Concerns\Plinkable;
use BenBjurstrom\Plink\Tests\Support\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends \Illuminate\Foundation\Auth\User implements Plinkable
{
    use HasFactory;
    use HasPlinks;
    use Notifiable;

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
