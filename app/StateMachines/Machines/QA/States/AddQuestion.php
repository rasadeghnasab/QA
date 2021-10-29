<?php

namespace App\StateMachines\Machines\QA\States;

use App\Models\Choice;
use App\Models\Question;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AddQuestion implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        $body = $this->command->ask('Enter the question body please');
        $answer = explode(',', $this->command->ask('Write all the choices separated by a comma. The first one is the correct one'));

        $this->validate(['question' => $body]);

        $correctChoice = array_shift($answer);

        $question = $this->command->user()->questions()->save(
            new Question([
                             'body' => $body,
                         ])
        );

        (new Choice(['title' => $correctChoice, 'correct' => true, 'question_id' => $question->id]))->save();
        foreach ($answer as $choice) {
            (new Choice(['title' => $choice, 'correct' => false, 'question_id' => $question->id]))->save();
        }

        $this->command->info('The question has been added successfully.');

        return $this->command->confirm('Add another one?', true) ? QAStatesEnum::AddQuestion : QAStatesEnum::MainMenu;
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    private function validate(array $data): void
    {
        $rules = [
            'question' => ['required', 'min:2', 'max:300'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::AddQuestion;
    }
}
