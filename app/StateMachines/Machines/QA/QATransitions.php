<?php

namespace App\StateMachines\Machines\QA;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\StateMachineMapInterface;
use App\StateMachines\Interfaces\TransitionInterface;
use App\StateMachines\Interfaces\TransitionsInterface;
use App\StateMachines\Transition;
use Exception;

class QATransitions implements TransitionsInterface
{
    private array $transitions = [];
    private StateMachineMapInterface $map;

    public function __construct(StateMachineMapInterface $map)
    {
        $this->map = $map;

        $this->fillTransitions($map);
    }

    public function next(StateInterface $state, string $action): StateInterface
    {
        foreach ($this->transitions as $transition) {
            if ($destination = $transition->destination($state, $action)) {
                return $destination;
            }
        }

        throw new Exception(
            sprintf('No path defined to any state from `%s` with the action `%s`', $state->name(), $action)
        );
    }

    public function transitions(): array
    {
        return $this->transitions;
    }

    public function addTransition(TransitionInterface $transition): void
    {
        $this->transitions[] = $transition;
    }

    public function addTransitions(array $transitions): void
    {
        foreach ($transitions as $transition) {
            $this->addTransition($transition);
        }
    }

    public function initialState(): StateInterface
    {
        return $this->map->initialState();
    }

    public function exitState(): StateInterface
    {
        return $this->map->exitState();
    }

    private function fillTransitions(StateMachineMapInterface $map): void
    {
        foreach ($map->path() as $transition) {
            $action = $transition['action'] ?? null;

            $this->addTransition(new Transition($transition['source'], $transition['destination'], $action));
        }
    }
}
