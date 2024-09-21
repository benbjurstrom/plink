<?php

namespace BenBjurstrom\Otpz\Commands;

use Illuminate\Console\Command;

class OtpzCommand extends Command
{
    public $signature = 'otpz';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
