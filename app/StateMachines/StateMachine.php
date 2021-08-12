<?php

namespace App\StateMachines;

use App\StateMachines\Interfaces\TransitionsInterface;
use Exception;
use App\StateMachines\Interfaces\MachineInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;

class StateMachine implements MachineInterface
{
    /**
     * @param Command $command
     * @param TransitionsInterface $transitions
     * @return int
     */
    public function start(Command $command, TransitionsInterface $transitions): int
    {
        $currentState = $transitions->initialState();
        $action = $currentState->action();

        do {
            try {
                $currentState = $transitions->next($currentState, $action);
//                $action = $currentState->handle();
                Artisan::call($currentState->signature(), ['user' => $command->user()]);
            } catch (ValidationException $validationException) {
                foreach (collect($validationException->errors())->flatten() as $error) {
                    $command->warn($error);
                }
            } catch (Exception $exception) {
                $command->error("Exiting...");
                $command->newLine();
                $command->error(sprintf('Error message: %s', $exception->getMessage()));

                $currentState = $transitions->exitState();

                return 255;
            }
        } while ($transitions->exitState()->action() !== $currentState->action());

        return 0;
    }
}
