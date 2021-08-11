<?php

namespace App\StateMachines\States;

use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;

class Practice implements StateInterface
{
    public function handle(Command $command): string
    {
        $practices = $command->user()->questions()->get(['id', 'body', 'status', 'answer']);

        $this->drawProgressTable($practices, $command);

        $this->askQuestion($practices, $command);

        $command->newLine(1);

        return $command->confirm('Continue?', true) ? 'Practice' : 'MainMenu';
    }

    public function name(): string
    {
        return self::class;
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

        $userAnswer = $command->ask($question->body);

        $status = 'Correct';
        if ($question->answer === $userAnswer) {
            $question->status = 'Correct';
            $command->info($status);
        } else {
            $question->status = 'Incorrect';
            $command->error($status);
        }

        $question->save();
    }
}
