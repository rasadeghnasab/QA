<?php

namespace App\StateMachines\Interfaces;

use Illuminate\Console\Command;

interface MachineInterface
{
    public function start(Command $command, TransitionsInterface $transitions): int;
}
