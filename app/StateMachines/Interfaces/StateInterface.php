<?php

namespace App\StateMachines\Interfaces;

use Illuminate\Console\Command;

interface StateInterface
{
    public function handle(Command $command): string;

    public function name(): string;

    public function action(): string;
}
