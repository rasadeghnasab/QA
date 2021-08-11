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
        $question = "Enter your email address\n If the email doesn't exist it will be created";
        $email = $command->ask($question, 'test@test.com');
        $user = User::where('email', $email)->first() ?? User::factory()->create(['email' => $email, 'name' => $email]);
        $authenticated = true;

        if (!$this->onlyEmail) {
            $password = $command->secret('Enter your password');
            $authenticated = Hash::check($password, $user->getAuthPassword());
        }

        if ($authenticated) {
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
