<?php

namespace BenBjurstrom\Plink\Actions;

use BenBjurstrom\Plink\Exceptions\PlinkThrottleException;
use BenBjurstrom\Plink\Mail\PlinkMail;
use BenBjurstrom\Plink\Models\Concerns\Plinkable;
use Illuminate\Support\Facades\Mail;

/**
 * @method static Plinkable run(string $email)
 *
 * @throws PlinkThrottleException
 */
class SendPlink
{
    public function handle(string $email, bool $remember = false): Plinkable
    {
        $mailable = config('plink.mailable', PlinkMail::class);
        $user = (new GetUserFromEmail)->handle($email);
        $plink = (new CreatePlink)->handle($user, $remember);

        Mail::to($user)->send(new $mailable($plink));

        return $user;
    }
}
