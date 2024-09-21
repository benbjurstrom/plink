<?php

namespace BenBjurstrom\Otpz\Actions;

use BenBjurstrom\Otpz\Enums\OtpStatus;
use BenBjurstrom\Otpz\Exceptions\OtpThrottleException;
use BenBjurstrom\Otpz\Models\Concerns\HasOtpsContract as User;
use Illuminate\Support\Str;

/**
 * @method static User run(User $user)
 */
class CreateOtp
{
    /**
     * @throws OtpThrottleException
     */
    public function handle(User $user): string
    {
        $this->throttle($user);

        return $this->createOtp($user);
    }

    /**
     * @throws OtpThrottleException
     */
    public function throttle(User $user)
    {
        foreach ($this->getThresholds() as $threshold) {
            $count = $this->getOtpCount($user, $threshold['minutes']);

            if ($count > $threshold['limit']) {
                $remaining = $this->calculateRemainingTime($user, $threshold['minutes']);
                throw new OtpThrottleException($remaining['minutes'], $remaining['seconds']);
            }
        }
    }

    private function getThresholds(): array
    {
        return [
            ['limit' => 1, 'minutes' => 1],
            ['limit' => 3, 'minutes' => 5],
            ['limit' => 5, 'minutes' => 30],
        ];
    }

    private function getOtpCount(User $user, int $minutes): int
    {
        return $user->otps()
            ->where('status', '!=', OtpStatus::USED)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    private function calculateRemainingTime(User $user, int $minutes): array
    {
        $earliestOtp = $user->otps()
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at', 'asc')
            ->first();

        if ($earliestOtp) {
            $availableAt = $earliestOtp->created_at->addMinutes($minutes);
            $remainingSeconds = now()->diffInSeconds($availableAt, false);

            return [
                'minutes' => floor($remainingSeconds / 60),
                'seconds' => $remainingSeconds % 60,
            ];
        }

        return ['minutes' => 0, 'seconds' => 0];
    }

    private function createOtp(User $user): string
    {
        // Generate a secure 6-digit OTP code
        $code = Str::upper(Str::random(9));

        // Invalidate existing active OTPs
        $user->otps()
            ->where('status', OtpStatus::ACTIVE)
            ->update(['status' => OtpStatus::SUPERSEDED]);

        // Create and save the new OTP
        $user->otps()->create([
            'code' => $code,
            'status' => OtpStatus::ACTIVE,
            'ip_address' => request()->ip(),
        ]);

        return $code;
    }
}
