<?php

namespace App\StateMachines\Interfaces;

interface StateMachineMapInterface
{
    public function states(): array;

    public function path(): array;

    public function initialState(): StateInterface;

    public function exitState(): StateInterface;
}
