<?php

namespace App\StateMachines\Interfaces;

use Illuminate\Console\Command;

interface StateMachineMapInterface
{
    public function __construct(Command $command);

    public function states(): array;

    public function path(): array;

    public function initialState(): StateInterface;

    public function exitState(): StateInterface;
}
