<?php

namespace BenBjurstrom\Otpz\Actions;

use BenBjurstrom\Otpz\Notifications\OtpNotification;
use BenBjurstrom\Otpz\Models\Concerns\Otpable;

/**
 * @method static Otpable run(string $email)
 */
class SendOtp
{
    public function handle(string $email): Otpable
    {
        $user = (new GetUserFromEmail)->handle($email);

        $otp = (new CreateOtp)->handle($user);
        $user->notify(new OtpNotification($otp));

        return $user;
    }
}
