<?php

declare(strict_types=1);

namespace BenBjurstrom\Plink\Actions;

use BenBjurstrom\Plink\Enums\OtpStatus;
use BenBjurstrom\Plink\Exceptions\OtpAttemptsException;
use BenBjurstrom\Plink\Models\Concerns\Otpable;
use BenBjurstrom\Plink\Models\Otp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * @method static Otpable run(Otpable $user, string $code)
 */
class AttemptOtp
{
    /**
     * @throws OtpAttemptsException
     */
    public function handle(Otpable $user, string $code): bool
    {
        $otp = $this->getOtp($user);

        $this->validateStatus($otp);
        $this->validateNotExpired($otp);
        $this->validateAttempts($otp);
        $this->validateCode($otp, $code);

        // if everything above passes mark the otp as used
        $otp->update(['status' => OtpStatus::USED]);

        return true;
    }

    protected function getOtp(Otpable $user): ?Otp
    {
        return $user->otps()
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * @throws OtpAttemptsException
     */
    protected function validateStatus(Otp $otp): void
    {
        if ($otp->status !== OtpStatus::ACTIVE) {
            throw new OtpAttemptsException($otp->status->errorMessage());
        }
    }

    /**
     * @throws OtpAttemptsException
     */
    protected function validateNotExpired(Otp $otp): void
    {
        if ($otp->created_at->lt(Carbon::now()->subMinutes(5))) {
            $otp->update(['status' => OtpStatus::EXPIRED]);
            throw new OtpAttemptsException($otp->status->errorMessage());
        }
    }

    /**
     * @throws OtpAttemptsException
     */
    protected function validateAttempts(Otp $otp): void
    {
        if ($otp->attempts >= 3) {
            $otp->update(['status' => OtpStatus::ATTEMPTED]);
            throw new OtpAttemptsException($otp->status->errorMessage());
        }
    }

    /**
     * @throws OtpAttemptsException
     */
    protected function validateCode(Otp $otp, string $code): void
    {
        if (! Hash::check($code, $otp->code)) {
            $otp->increment('attempts');
            throw new OtpAttemptsException(OtpStatus::INVALID->errorMessage());
        }
    }
}
