<?php

namespace App\StateMachines;

use Exception;
use App\StateMachines\Interfaces\MachineInterface;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\TransitionInterface;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

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

    /**
     * @param string $action
     * @return StateInterface
     * @throws Exception
     */
    public function next(string $action): StateInterface
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

    public function start(Command $command): void
    {
        $action = $this->currentState->action();

        do {
            try {
                $this->currentState = $this->next($action);
                $action = $this->currentState->handle($command);
            } catch (ValidationException $validationException) {
                foreach (collect($validationException->errors())->flatten() as $error) {
                    $command->warn($error);
                }
            } catch (Exception $exception) {
                $command->error("Exiting...");
                $command->newLine();
                $command->error(sprintf('Error message: %s', $exception->getMessage()));

                $this->currentState = $this->exitState;
                exit(255);
            }
        } while ($this->exitState->action() !== $this->currentState->action());
    }
}
