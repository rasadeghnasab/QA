<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Practice extends Command implements StateInterface
{
    public function handle(): string
    {
        $practices = $this->user()->questions()->get(['id', 'body', 'status', 'answer']);

        if (empty($practices)) {
            $this->warn('No question to answer.');
            $result = $this->confirm('Want to Add one?', true);

            return $result ? QAStatesEnum::AddQuestion : QAStatesEnum::MainMenu;
        }

        $this->drawProgressTable($practices);

        $this->askQuestion($practices);

        return $this->confirm('Continue?', true) ? QAStatesEnum::Practice : QAStatesEnum::MainMenu;
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
        $completed = 0;

        if ($total = $practices->count()) {
            $completed = number_format($correct->count() * 100 / $total);
        }

        $progress = sprintf('%%%d answered correctly', $completed);

        $this->table(
            ['ID', 'Question', 'Status'],
            $practices->map(function ($question) {
                return $question->only(['id', 'body', 'status']);
            }),
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

        $selected = $this->choice('Choose one of the questions above',
            $notCorrectPractices->pluck('body', 'id')->toArray(),
            $firstNotCorrect->id ?? null,
        );

        $question = $notCorrectPractices->where('body', $selected)->first();

        $userAnswer = $this->getInputs($question->body);

        if ($question->answer === $userAnswer) {
            $status = 'Correct';
            $this->info($status);
        } else {
            $status = 'Incorrect';
            $this->error($status);
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
                $answer = $this->ask($questionBody);

                $this->validate(['answer' => $answer]);
            } catch (ValidationException $validationException) {
                foreach (collect($validationException->errors())->flatten() as $error) {
                    $this->warn($error);
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
