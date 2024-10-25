<?php

namespace BenBjurstrom\Plink\Actions;

use BenBjurstrom\Plink\Models\Concerns\Plinkable;
use BenBjurstrom\Plink\Support\Config;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;

/**
 * @method static Plinkable run(string $email)
 */
class GetUserFromEmail
{
    public function handle(string $email): Plinkable
    {
        $authenticatableModel = Config::getAuthenticatableModel();
        $user = $authenticatableModel::where('email', $email)->first();

        if (! $user) {
            $user = new $authenticatableModel;
            $user->email = $email;
            $user->password = Str::random(32);
            $user->name = '';
            $user->save();

            event(new Registered($user));
        }

        return $user;
    }
}
