<?php

namespace App\StateMachines\States;

use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;

class ExitApp implements StateInterface
{
    public function handle(Command $command): bool
    {
        $command->info('Goodbye my friend.');

        return false;
    }

    public function getName(): string
    {
        return self::class;
    }
}
