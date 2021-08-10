<?php

namespace App\Console\Commands;

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
                do {
                    $continue = $this->practice();
                } while ($continue === true);
            } elseif ($input === 'Stats') {
                $this->stats();
            } elseif ($input === 'Reset') {
                $this->reset();
            }
        } while ($this->shouldStay($input));
    }

    private function reset()
    {
        if (!$this->confirm('Are you sure? (You can not undo this action')) {
            return false;
        }

        $this->user->questions()->update(['status' => 'Not answered']);
        $this->warn('Your questions are marked as `Not answered`.');
    }

    private function stats()
    {
        $questions = $this->user->questions()->get();

        $all = $questions->count();
        $answered = $questions->where('status', 'Incorrect')->count();
        $correct = $questions->where('status', 'Correct')->count();

        $this->titledTable(['Header', 'Value'], [
            ['Total', $questions->count()],
            ['Answered', sprintf('%%%s', number_format($answered * 100 / $all))],
            ['Correct', sprintf('%%%s', number_format($correct * 100 / $all))]
        ],
            'Stats'
        );
        $this->newLine();
    }

    public function practice()
    {
        $practices = $this->user->questions()->get(['id', 'body', 'status', 'answer']);

        $correct = $practices->where('status', 'Correct');

        $completion = sprintf('%%%d', number_format($correct->count() * 100 / $practices->count()));

        $this->titledTable(
            ['ID', 'Question', 'Status'],
            $practices->map(function ($question) {
                return $question->only(['id', 'body', 'status']);
            }),
            'Practices',
            $completion
        );

        $notCorrectPractices = $practices->where('status', '!=', 'Correct');
        $firstNotCorrect = $notCorrectPractices->first();


        $selected = $this->choice('Choose one of the question above',
            $notCorrectPractices->pluck('body', 'id')->toArray(),
            $firstNotCorrect->id,
        );

        $question = $notCorrectPractices->where('body', $selected)->first();

        $userAnswer = $this->ask($question->body);

        $status = 'Correct';
        if ($question->answer === $userAnswer) {
            $this->info($status);
        } else {
            $status = 'Incorrect';
            $this->error($status);
        }

        $question->status = $status;
        $question->save();

        $this->newLine(2);

        return $this->confirm('Continue?', true);
    }

    private function listQuestions()
    {
        $questions = $this->user->questions()->get(['id', 'body'])->toArray();

        $this->titledTable(
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

        $this->info('The question has been added successfully.');

        return $this->confirm('Add another one?', true);
    }

    private function shouldStay($input): bool
    {
        if ($input !== 'Exit') {
            return true;
        }

        $this->info(sprintf('Good by my friend'));

        return false;
    }

    private function mainMenu()
    {
        $defaultIndex = 0;
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

        $this->clearScreen();

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
     * @return void
     */
    public function titledTable($headers, $rows, string $header = '', string $footer = '', $tableStyle = 'default')
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

    private function clearScreen()
    {
        system('clear');
    }
}
