<?php

namespace BenBjurstrom\Otpz\Actions;

use BenBjurstrom\Otpz\Models\Concerns\Otpable;
use BenBjurstrom\Otpz\Notifications\OtpNotification;

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
