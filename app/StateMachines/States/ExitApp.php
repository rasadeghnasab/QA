<?php

namespace App\StateMachines\States;

use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;

class ExitApp implements StateInterface
{
    public function handle(Command $command): string
    {
        $command->info(sprintf('Goodbye `%s`.', $command->user()->name));

        return 'Exit';
    }

    public function name(): string
    {
        return self::class;
    }
}
