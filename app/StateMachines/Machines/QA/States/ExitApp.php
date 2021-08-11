<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class ExitApp implements StateInterface
{
    public function handle(Command $command): string
    {
        $command->info(sprintf('Goodbye `%s`.', $command->user()->name));

        return QAStatesEnum::Exit;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Exit;
    }
}
