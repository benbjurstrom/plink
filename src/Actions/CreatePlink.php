<?php

namespace BenBjurstrom\Plink\Actions;

use BenBjurstrom\Plink\Enums\PlinkStatus;
use BenBjurstrom\Plink\Exceptions\PlinkThrottleException;
use BenBjurstrom\Plink\Models\Concerns\Plinkable;
use BenBjurstrom\Plink\Models\Plink;

/**
 * @method static Plinkable run(Plinkable $user)
 */
class CreatePlink
{
    /**
     * @throws PlinkThrottleException
     */
    public function handle(Plinkable $user): Plink
    {
        $this->throttle($user);

        return $this->createPlink($user);
    }

    /**
     * @throws PlinkThrottleException
     */
    public function throttle(Plinkable $user)
    {
        foreach ($this->getThresholds() as $threshold) {
            $count = $this->getPlinkCount($user, $threshold['minutes']);

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

    private function getPlinkCount(Plinkable $user, int $minutes): int
    {
        return $user->plinks()
            ->where('status', '!=', PlinkStatus::USED)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    private function calculateRemainingTime(Plinkable $user, int $minutes): array
    {
        $earliestPlink = $user->plinks()
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at', 'asc')
            ->first();

        if ($earliestPlink) {
            $availableAt = $earliestPlink->created_at->addMinutes($minutes);
            $remainingSeconds = now()->diffInSeconds($availableAt, false);

            return [
                'minutes' => floor($remainingSeconds / 60),
                'seconds' => $remainingSeconds % 60,
            ];
        }

        return ['minutes' => 0, 'seconds' => 0];
    }

    private function createPlink(Plinkable $user): Plink
    {
        // Invalidate existing active plinks
        $user->plinks()
            ->where('status', PlinkStatus::ACTIVE)
            ->update(['status' => PlinkStatus::SUPERSEDED]);

        // Create and save the new plink
        $plink = $user->plinks()->create([
            'status' => PlinkStatus::ACTIVE,
            'ip_address' => request()->ip(),
        ]);

        return $plink;
    }
}
