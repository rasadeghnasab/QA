<?php

namespace App\StateMachines\States;

use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;

class Practice implements StateInterface
{
    public function handle(Command $command): string|bool
    {
        $practices = $command->user()->questions()->get(['id', 'body', 'status', 'answer']);

        $correct = $practices->where('status', 'Correct');

        $completion = sprintf('%%%d', number_format($correct->count() * 100 / $practices->count()));

        $command->titledTable(
            ['ID', 'Question', 'Status'],
            $practices->map(function ($question) {
                return $question->only(['id', 'body', 'status']);
            }),
            'Practices',
            $completion
        );

        $notCorrectPractices = $practices->where('status', '!=', 'Correct');
        $firstNotCorrect = $notCorrectPractices->first();


        $selected = $command->choice('Choose one of the question above',
            $notCorrectPractices->pluck('body', 'id')->toArray(),
            $firstNotCorrect->id,
        );

        $question = $notCorrectPractices->where('body', $selected)->first();

        $userAnswer = $command->ask($question->body);

        $status = 'Correct';
        if ($question->answer === $userAnswer) {
            $command->info($status);
        } else {
            $status = 'Incorrect';
            $command->error($status);
        }

        $question->status = $status;
        $question->save();

        $command->newLine(2);

        return $command->confirm('Continue?', true) ? 'Continue' : 'MainMenu';
    }

    public function getName(): string
    {
        return self::class;
    }
}
