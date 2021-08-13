<?php

namespace Tests\Unit;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\TransitionsInterface;
use App\StateMachines\StateMachine;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\Feature\QATestCase;

class StateMachineTest extends QATestCase
{
    use RefreshDatabase;

    public function test_should_exit_with_255_on_exception(): void
    {
        $errorMessage = 'A simple error message';

        $command = Mockery::mock(Command::class)
            ->shouldReceive(['error' => '', 'newLine' => ''])
            ->getMock();

        $state = Mockery::mock(StateInterface::class)
            ->shouldReceive('action')->andReturn('anything')
            ->shouldReceive('handle')->andThrow(new Exception($errorMessage))
            ->getMock();

        $transitions = Mockery::mock(TransitionsInterface::class)
            ->shouldReceive([
                'next' => $state,
                'initialState' => $state,
                'exitState' => $state,
            ])
            ->getMock();

        $stateMachine = new StateMachine;
        $exitCode = $stateMachine->start($command, $transitions);

        $this->assertEquals(255, $exitCode);
    }
}
