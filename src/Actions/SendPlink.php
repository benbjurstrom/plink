<?php

namespace BenBjurstrom\Plink\Actions;

use BenBjurstrom\Plink\Models\Concerns\Plinkable;
use BenBjurstrom\Plink\Notifications\PlinkNotification;

/**
 * @method static Plinkable run(string $email)
 */
class SendPlink
{
    public function handle(string $email): Plinkable
    {
        $user = (new GetUserFromEmail)->handle($email);

        $plink = (new CreatePlink)->handle($user);
        $user->notify(new PlinkNotification($plink));

        return $user;
    }
}
