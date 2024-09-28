<?php

namespace BenBjurstrom\Plink\Actions;

use BenBjurstrom\Plink\Models\Concerns\Otpable;
use BenBjurstrom\Plink\Notifications\OtpNotification;

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
