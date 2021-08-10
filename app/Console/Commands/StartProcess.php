<?php

namespace App\Console\Commands;

use App\Models\Practice;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Question;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Helper\Table;

class StartProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->user = User::where('id', 1)->first() ?? new User(['id' => 1]);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        do {
            $input = $this->mainMenu();

            if ($input === 'Create a question') {
                do {
                    $continue = $this->addAQuestion();
                } while ($continue === true);
            } elseif ($input === 'List all questions') {
                $this->listQuestions();
            } elseif ($input === 'Practice') {
                $this->practice();
            }

            $stay = $this->shouldStay($input);
        } while ($stay);
    }

    public function practice()
    {
//        $practices = $this->user->practices()->with('question')->get();
//        $questions = Question::whereHas('practices', function ($query) {
//            $query->where('user_id', $this->user->id);
//        })->get();

        $questions = $this->user->questions()->with('practices')->get();
//        dd($this->user->questions->pluck('id'));
//        dd(Practice::whereIn('question_id', $this->user->questions->pluck('id'))->get()->toArray());
//        $questions = Question::where('with('practices')
        dd($questions->toArray());
//        dd($practices->toArray());

        $this->customTable();
    }

    private function listQuestions()
    {
        $questions = Question::get(['id', 'body'])->toArray();

        $this->customTable(
            ['ID', 'Question'],
            $questions,
            'borderless',
            'Questions',
        );
    }

    private function addAQuestion()
    {
        $body = $this->ask('Enter your question body please');

        $answer = $this->ask('Enter the answer for your question');

        $this->user->questions()->save(
            new Question([
                'body' => $body,
                'answer' => $answer,
            ])
        );

        $this->newLine();
        $this->question($body);
        $this->question($answer);
        $this->newLine(2);

        return $this->continue();
    }

    private function shouldStay($input): bool
    {
        if ($input !== 'Exit') {
            return true;
        }

        $this->error(sprintf('Good by my friend'));

        return false;
    }

    private function continue(): bool
    {
        $defaultIndex = 'Continue';
        $choice = $this->choice('Do you want to continue?',
            [
                'Continue',
                'Back',
            ], $defaultIndex);

        return $choice === 'Continue';
    }

    private function mainMenu()
    {
        $defaultIndex = 1;
        $choice = $this->choice(
            'Choose one option',
            [
                'Create a question',
                'List all questions',
                'Practice',
                'Stats',
                'Reset',
                'Exit'
            ],
            $defaultIndex
        );

        $this->info(sprintf('You choose %s', $choice));

        return $choice;
    }

    /**
     * Format input to textual table.
     *
     * @param array $headers
     * @param \Illuminate\Contracts\Support\Arrayable|array $rows
     * @param string $tableStyle
     * @param string $header
     * @param string $footer
     * @param array $columnStyles
     * @return void
     */
    public function customTable($headers, $rows, $tableStyle = 'default', string $header = '', string $footer = '')
    {
        $table = new Table($this->output);

        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }

        $table->setHeaders((array)$headers)->setRows($rows)->setStyle($tableStyle);

        if (!empty($header)) {
            $table->setHeaderTitle($header);
        }

        if (!empty($footer)) {
            $table->setFooterTitle($footer);
        }

        $table->render();
    }

}
