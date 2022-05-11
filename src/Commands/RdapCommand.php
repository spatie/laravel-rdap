<?php

namespace Spatie\Rdap\Commands;

use Illuminate\Console\Command;

class RdapCommand extends Command
{
    public $signature = 'laravel-rdap';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
