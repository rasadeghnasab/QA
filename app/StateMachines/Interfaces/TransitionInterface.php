<?php

namespace App\StateMachines\Interfaces;

interface TransitionInterface
{
    public function destination(StateInterface $currentStateState, string $action): StateInterface|bool;
}
