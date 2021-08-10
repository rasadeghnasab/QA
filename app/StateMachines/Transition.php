<?php

namespace App\StateMachines;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\TransitionInterface;

class Transition implements TransitionInterface
{
    private string $action;
    private StateInterface $source;
    private StateInterface $destination;

    public function __construct(string $action, StateInterface $source, StateInterface $destination)
    {
        $this->action = $action;
        $this->source = $source;
        $this->destination = $destination;
    }

    public function destination(StateInterface $current, string $action): StateInterface|bool
    {
        if ($this->source->getName() === $current->getName() && $this->action === $action) {
            return $this->destination;
        }

        return false;
    }
}
