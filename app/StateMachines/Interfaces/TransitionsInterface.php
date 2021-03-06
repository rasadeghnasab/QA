<?php

namespace App\StateMachines\Interfaces;

interface TransitionsInterface
{
    /**
     * @param StateInterface $state
     * @param string $action
     * @return StateInterface
     * @throw Exception
     */
    public function next(StateInterface $state, string $action): StateInterface;

    public function transitions(): array;

    public function initialState(): StateInterface;

    public function exitState(): StateInterface;
}
