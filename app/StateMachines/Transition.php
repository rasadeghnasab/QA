<?php

namespace App\StateMachines;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\TransitionInterface;

class Transition implements TransitionInterface
{
    private string $action;
    private StateInterface $source;
    private StateInterface $destination;

    public function __construct(StateInterface $source, StateInterface $destination, string $action = null)
    {
        $this->action = $action ?? $destination->action();
        $this->source = $source;
        $this->destination = $destination;
    }

    public function destination(StateInterface $currentState, string $action): StateInterface|bool
    {
        if ($this->source->name() === $currentState->name() && $this->action === $action) {
            return $this->destination;
        }

        return false;
    }
}
