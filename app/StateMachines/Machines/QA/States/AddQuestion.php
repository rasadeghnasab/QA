<?php

namespace App\StateMachines\Machines\QA\States;

use App\Models\Question;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AddQuestion implements StateInterface
{
    public function handle(Command $command): string
    {
        $body = $command->ask('Enter your question body please');
        $answer = $command->ask('Enter the answer for your question');

        $this->validate(['question' => $body, 'answer' => $answer]);

        $command->user()->questions()->save(
            new Question([
                'body' => $body,
                'answer' => $answer,
            ])
        );

        $command->info('The question has been added successfully.');

        return $command->confirm('Add another one?', true) ? QAStatesEnum::AddQuestion : QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::AddQuestion;
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    private function validate(array $data): void
    {
        $rules = [
            'question' => ['required', 'min:2', 'max:300'],
            'answer' => ['required', 'min:2', 'max:300'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
