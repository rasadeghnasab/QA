<?php

namespace App\StateMachines\States;

use App\Models\User;
use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class Authenticate implements StateInterface
{
    private bool $onlyEmail;

    public function handle(Command $command): string
    {
        $email = $command->ask('Enter your email address', 'test@test.com');
        $user = User::where('email', $email)->first();
        $isValid = true;

        if (!$this->onlyEmail) {
            $password = $command->secret('Enter your password');
            $isValid = Hash::check($password, $user->getAuthPassword());
        }

        if ($isValid) {
            $command->info('You logged in successfully');
            $command->setUser($user);
            return 'MainMenu';
        }

        $command->error('Authentication failed. Please try again.');

        return 'Authenticate';
    }

    public function name(): string
    {
        return self::class;
    }

    public function onlyEmail(): self
    {
        $this->onlyEmail = true;

        return $this;
    }

    public function fullCredentials(): self
    {
        $this->onlyEmail = false;

        return $this;
    }
}
