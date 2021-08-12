<?php

namespace App\StateMachines\Interfaces;

interface StateInterface
{
    public function handle(): string;

    public function name(): string;

    public function action(): string;
}
