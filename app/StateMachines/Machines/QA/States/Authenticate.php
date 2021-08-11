<?php

namespace App\StateMachines\Machines\QA\States;

use App\Models\User;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Authenticate implements StateInterface
{
    private bool $onlyEmail = false;

    public function handle(Command $command): string
    {
        list($email, $password) = $this->getInputs($command);

        $user = User::where('email', $email)->first() ?? User::factory()->create(['email' => $email, 'name' => $email]);
        $authenticated = true;

        if (!$this->onlyEmail) {
            $authenticated = Hash::check($password, $user->getAuthPassword());
        }

        if ($authenticated) {
            $command->info(' You logged in successfully');
            $command->newLine();
            $command->setUser($user);

            return QAStatesEnum::MainMenu;
        }

        $command->error('Authentication failed. Please try again.');

        return QAStatesEnum::Authenticate;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Authenticate;
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

    /**
     * @param array $data
     * @throws ValidationException
     */
    private function validate(array $data): void
    {
        $rules = [
            'email' => ['required', 'email'],
        ];

        if (array_key_exists('password', $data)) {
            $rules['password'] = ['required', 'min:8'];
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function getInputs(Command $command): array
    {
        $question = "Enter your email address\n If the email doesn't exist it will be created";
        $email = $command->ask($question, 'test@test.com');
        $data = ['email' => $email];

        $password = '';
        if (!$this->onlyEmail) {
            $password = $command->secret('Enter your password');
            $data['password'] = $password;
        }

        $this->validate($data);

        return [$email, $password];
    }
}
