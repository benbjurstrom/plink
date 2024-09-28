<?php

namespace BenBjurstrom\Plink\Actions;

use BenBjurstrom\Plink\Enums\PlinkStatus;
use BenBjurstrom\Plink\Exceptions\PlinkThrottleException;
use BenBjurstrom\Plink\Models\Concerns\Plinkable;
use Illuminate\Support\Str;

/**
 * @method static Plinkable run(Plinkable $user)
 */
class CreatePlink
{
    /**
     * @throws PlinkThrottleException
     */
    public function handle(Plinkable $user): string
    {
        $this->throttle($user);

        return $this->createOtp($user);
    }

    /**
     * @throws PlinkThrottleException
     */
    public function throttle(Plinkable $user)
    {
        foreach ($this->getThresholds() as $threshold) {
            $count = $this->getOtpCount($user, $threshold['minutes']);

            if ($count > $threshold['limit']) {
                $remaining = $this->calculateRemainingTime($user, $threshold['minutes']);
                throw new PlinkThrottleException($remaining['minutes'], $remaining['seconds']);
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

    private function getOtpCount(Plinkable $user, int $minutes): int
    {
        return $user->plinks()
            ->where('status', '!=', PlinkStatus::USED)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    private function calculateRemainingTime(Plinkable $user, int $minutes): array
    {
        $earliestOtp = $user->plinks()
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

    private function createOtp(Plinkable $user): string
    {
        // Generate a secure 6-digit OTP code
        $code = Str::upper(Str::random(9));

        // Invalidate existing active OTPs
        $user->plinks()
            ->where('status', PlinkStatus::ACTIVE)
            ->update(['status' => PlinkStatus::SUPERSEDED]);

        // Create and save the new OTP
        $user->plinks()->create([
            'code' => $code,
            'status' => PlinkStatus::ACTIVE,
            'ip_address' => request()->ip(),
        ]);

        return $code;
    }
}
