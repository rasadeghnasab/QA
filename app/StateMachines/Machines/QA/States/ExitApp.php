<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class ExitApp extends Command implements StateInterface
{
    public function handle(): string
    {
        $this->info(sprintf('Goodbye `%s`.', $this->user()->name));

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
