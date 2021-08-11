<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Practice implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        $practices = $this->command->user()->questions()->get(['id', 'body', 'status', 'answer']);

        $this->drawProgressTable($practices);

        $this->askQuestion($practices);

        return $this->command->confirm('Continue?', true) ? QAStatesEnum::Practice : QAStatesEnum::MainMenu;
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
     */
    private function drawProgressTable($practices): void
    {
        $correct = $practices->where('status', 'Correct');

        $completion = sprintf('%%%d answered correctly', number_format($correct->count() * 100 / $practices->count()));

        $this->command->titledTable(
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
     * @return void
     */
    private function askQuestion($practices): void
    {
        $notCorrectPractices = $practices->where('status', '!=', 'Correct');
        $firstNotCorrect = $notCorrectPractices->first();

        $selected = $this->command->choice('Choose one of the questions above',
            $notCorrectPractices->pluck('body', 'id')->toArray(),
            $firstNotCorrect->id,
        );

        $question = $notCorrectPractices->where('body', $selected)->first();

        $userAnswer = $this->getInputs($question->body);

        if ($question->answer === $userAnswer) {
            $status = 'Correct';
            $this->command->info($status);
        } else {
            $status = 'Incorrect';
            $this->command->error($status);
        }

        $question->status = $status;
        $question->save();
    }

    private function getInputs(string $questionBody): string
    {
        $answer = '';

        do {
            try {
                $valid = true;
                $answer = $this->command->ask($questionBody);

                $this->validate(['answer' => $answer]);
            } catch (ValidationException $validationException) {
                foreach (collect($validationException->errors())->flatten() as $error) {
                    $this->command->warn($error);
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
