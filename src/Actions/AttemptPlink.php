<?php

declare(strict_types=1);

namespace BenBjurstrom\Plink\Actions;

use BenBjurstrom\Plink\Enums\PlinkStatus;
use BenBjurstrom\Plink\Exceptions\PlinkAttemptsException;
use BenBjurstrom\Plink\Models\Concerns\Plinkable;
use BenBjurstrom\Plink\Models\Plink;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * @method static Plinkable run(Plinkable $user, string $code)
 */
class AttemptPlink
{
    /**
     * @throws PlinkAttemptsException
     */
    public function handle(Plinkable $user, string $code): bool
    {
        $plink = $this->getOtp($user);

        $this->validateStatus($plink);
        $this->validateNotExpired($plink);
        $this->validateAttempts($plink);
        $this->validateCode($plink, $code);

        // if everything above passes mark the plink as used
        $plink->update(['status' => PlinkStatus::USED]);

        return true;
    }

    protected function getOtp(Plinkable $user): ?Plink
    {
        return $user->plinks()
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * @throws PlinkAttemptsException
     */
    protected function validateStatus(Plink $plink): void
    {
        if ($plink->status !== PlinkStatus::ACTIVE) {
            throw new PlinkAttemptsException($plink->status->errorMessage());
        }
    }

    /**
     * @throws PlinkAttemptsException
     */
    protected function validateNotExpired(Plink $plink): void
    {
        if ($plink->created_at->lt(Carbon::now()->subMinutes(5))) {
            $plink->update(['status' => PlinkStatus::EXPIRED]);
            throw new PlinkAttemptsException($plink->status->errorMessage());
        }
    }

    /**
     * @throws PlinkAttemptsException
     */
    protected function validateAttempts(Plink $plink): void
    {
        if ($plink->attempts >= 3) {
            $plink->update(['status' => PlinkStatus::ATTEMPTED]);
            throw new PlinkAttemptsException($plink->status->errorMessage());
        }
    }

    /**
     * @throws PlinkAttemptsException
     */
    protected function validateCode(Plink $plink, string $code): void
    {
        if (! Hash::check($code, $plink->code)) {
            $plink->increment('attempts');
            throw new PlinkAttemptsException(PlinkStatus::INVALID->errorMessage());
        }
    }
}
