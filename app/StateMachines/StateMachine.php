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
    private StateInterface $exitState;

    public function setInitialState(StateInterface $state): void
    {
        $this->currentState = $state;
    }

    public function setExitState(StateInterface $state): void
    {
        $this->exitState = $state;
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

        throw new Exception(
            sprintf('No path defined to any state from `%s` with the action `%s`', $this->currentState->name(), $action)
        );
    }

    public function start(Command $command)
    {
        $action = $this->currentState->handle($command);

        while ($this->exitState->name() != $this->currentState->name()) {
            try {
                $state = $this->next($action);
                $this->currentState = $state;
                $action = $state->handle($command);
            } catch (Exception $exception) {
//                $command->error(sprintf("Exiting...\n%s", $exception->getMessage()));
                $command->error("Exiting...");
                $command->newLine();
                $command->error(sprintf('Error message: %s', $exception->getMessage()));

                $this->currentState = $this->exitState;
                exit(255);
            }
        }
    }
}
