<?php

namespace App\StateMachines\States;

use App\StateMachines\Interfaces\StateInterface;
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

        return 'MainMenu';
    }

    public function name(): string
    {
        return self::class;
    }
}
