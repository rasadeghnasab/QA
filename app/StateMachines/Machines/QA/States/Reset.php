<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class Reset implements StateInterface
{
    public function handle(Command $command): string
    {
        if (!$command->confirm('Are you sure? (You can not undo this action')) {
            return false;
        }

        $command->user()->questions()->update(['status' => 'Not answered']);
        $command->warn('Your questions are marked as `Not answered`.');

        return QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Reset;
    }
}
