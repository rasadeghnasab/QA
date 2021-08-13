<?php

namespace Tests\Unit;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\StateMachineMapInterface;
use App\StateMachines\Machines\QA\QATransitions;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class QATransitionsTest extends TestCase
{
    private QATransitions $transitions;
    private array $states;
    private array $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->states = [];
        for ($i = 0; $i < 4; $i++) {

            $this->states[$i] = Mockery::mock(StateInterface::class)
                ->shouldReceive('action')->andReturn($i)
                ->shouldReceive('name')->andReturn($i)
                ->getMock();
        }

        $this->path = [
            [
                'source' => $this->states[0],
                'destination' => $this->states[1],
            ],
            [
                'source' => $this->states[1],
                'destination' => $this->states[2],
            ],
            [
                'source' => $this->states[1],
                'destination' => $this->states[3]
            ],
            [
                'source' => $this->states[3],
                'destination' => $this->states[1],
            ]
        ];

        $map = $this->mock(StateMachineMapInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('path')->andReturn($this->path);
        });

        $this->transitions = new QATransitions($map);
    }

    public function test_should_find_next_state_based_on_the_given_action(): void
    {
        // from 0 to 1 with action 1
        $destination = $this->transitions->next($this->states[0], '1');
        $this->assertEquals($this->states[1], $destination);

        // from 1 to 2 with action 2
        $destination = $this->transitions->next($this->states[1], '2');
        $this->assertEquals($this->states[2], $destination);

        // from 1 to 2 with action 2
        $destination = $this->transitions->next($this->states[1], '3');
        $this->assertEquals($this->states[3], $destination);

        // from 1 to 2 with action 2
        $destination = $this->transitions->next($this->states[3], '1');
        $this->assertEquals($this->states[1], $destination);

        $this->assertCount(4, $this->path);
    }

    public function test_should_throw_exception_on_undefined_path(): void
    {
        $currentState = $this->states[0];
        $action = 'any';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('No path defined to any state from `%s` with the action `%s`', $currentState->name(), $action));

        $this->transitions->next($currentState, $action);
    }

    public function test_should_no_path_from_state_2_to_anywhere(): void
    {
        $currentState = $this->states[2];
        $action = 'any';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('No path defined to any state from `%s` with the action `%s`', $currentState->name(), $action));

        $this->transitions->next($currentState, $action);
    }
}
