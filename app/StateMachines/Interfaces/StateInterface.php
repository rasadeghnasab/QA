<?php

namespace App\StateMachines\Interfaces;

use Illuminate\Console\Command;

interface StateInterface
{
    public function __construct(Command $command);

    public function handle(): string;

    public function name(): string;

    public function action(): string;
}
