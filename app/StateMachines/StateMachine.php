<?php

namespace App\StateMachines;

use Exception;
use App\StateMachines\Interfaces\MachineInterface;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\TransitionInterface;
use Illuminate\Console\Command;

class StateMachine implements MachineInterface
{
    private array $transitions = [];
    private StateInterface $currentState;

    public function setInitialState(StateInterface $state): void
    {
        $this->currentState = $state;
    }

    public function addTransition(TransitionInterface $transition): void
    {
        $this->transitions[] = $transition;
    }

    public function next(string $action): StateInterface|Exception
    {
        foreach ($this->transitions as $transition) {
            if ($destination = $transition->destination($this->currentState, $action)) {
                return $destination;
            }
        }

        throw new Exception('This state has no path to anywhere.');
    }

    public function start(Command $command)
    {
        $action = $this->currentState->handle($command);
        while ($action !== 'Exit') {
            try {
                $state = $this->next($action);
                $this->currentState = $state;
                $action = $state->handle($command);
            } catch (Exception $exception) {
                $command->error($exception->getMessage());
                $action = 'Exit';
                exit(255);
            }
        }
    }
}
