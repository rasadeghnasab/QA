<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Practice implements StateInterface
{
    public function handle(Command $command): string
    {
        $practices = $command->user()->questions()->get(['id', 'body', 'status', 'answer']);

        $this->drawProgressTable($practices, $command);

        $this->askQuestion($practices, $command);

        return $command->confirm('Continue?', true) ? QAStatesEnum::Practice : QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Practice;
    }

    /**
     * @param $practices
     * @param Command $command
     */
    private function drawProgressTable($practices, Command $command): void
    {
        $correct = $practices->where('status', 'Correct');

        $completion = sprintf('%%%d answered correctly', number_format($correct->count() * 100 / $practices->count()));

        $command->titledTable(
            ['ID', 'Question', 'Status'],
            $practices->map(function ($question) {
                return $question->only(['id', 'body', 'status']);
            }),
            'Practices',
            $completion
        );
    }

    /**
     * @param $practices
     * @param Command $command
     * @return void
     */
    private function askQuestion($practices, Command $command): void
    {
        $notCorrectPractices = $practices->where('status', '!=', 'Correct');
        $firstNotCorrect = $notCorrectPractices->first();

        $selected = $command->choice('Choose one of the questions above',
            $notCorrectPractices->pluck('body', 'id')->toArray(),
            $firstNotCorrect->id,
        );

        $question = $notCorrectPractices->where('body', $selected)->first();

        $userAnswer = $this->getInputs($command, $question->body);

        if ($question->answer === $userAnswer) {
            $status = 'Correct';
            $command->info($status);
        } else {
            $status = 'Incorrect';
            $command->error($status);
        }

        $question->status = $status;
        $question->save();
    }

    private function getInputs(Command $command, string $questionBody): string
    {
        $answer = '';

        do {
            try {
                $valid = true;
                $answer = $command->ask($questionBody);

                $this->validate(['answer' => $answer]);
            } catch (ValidationException $validationException) {
                foreach (collect($validationException->errors())->flatten() as $error) {
                    $command->warn($error);
                }
                $valid = false;
            }
        } while (!$valid);

        return $answer;
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    private function validate(array $data): void
    {
        $rules = [
            'answer' => ['required', 'min:2', 'max:300'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
