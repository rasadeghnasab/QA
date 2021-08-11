<?php

namespace App\StateMachines\Interfaces;

use Illuminate\Console\Command;

interface MachineInterface
{
    public function setInitialState(StateInterface $state): void;

    public function setExitState(StateInterface $state): void;

    public function addTransition(TransitionInterface $transition): void;

    public function start(Command $command): int;
}
