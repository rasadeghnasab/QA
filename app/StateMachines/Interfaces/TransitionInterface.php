<?php

namespace App\StateMachines\Interfaces;

interface TransitionInterface
{
    public function destination(StateInterface $currentState, string $action): StateInterface|bool;
}
