<?php

namespace BenBjurstrom\Otpz\Actions;

use BenBjurstrom\Otpz\Support\Config;
use BenBjurstrom\Otpz\Models\Concerns\HasOtpsContract as User;
use Illuminate\Support\Str;

/**
 * @method static User run(string $email)
 */
class GetUserFromEmail
{
    public function handle(string $email): User
    {
        $authenticatableModel = Config::getAuthenticatableModel();
        $user = $authenticatableModel::where('email', $email)->first();

        if (! $user) {
            $user = new $authenticatableModel;
            $user->email = $email;
            $user->password = Str::random(32);
            $user->name = '';
            $user->save();
        }

        return $user;
    }
}
