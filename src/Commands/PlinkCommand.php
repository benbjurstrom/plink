<?php

namespace BenBjurstrom\Plink\Commands;

use Illuminate\Console\Command;

class PlinkCommand extends Command
{
    public $signature = 'plink';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
