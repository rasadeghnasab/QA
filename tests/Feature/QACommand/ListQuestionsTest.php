<?php

namespace Tests\Feature\QACommand;

use App\Models\Question;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class ListQuestionsTest extends QATestCase
{
    use RefreshDatabase;

    public function test_user_has_no_question_yet(): void
    {
        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::ListQuestions, QAStatesEnum::mainMenu())
            ->expectsOutput('You do not have any question!')
            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu());
    }

    /**
     * @dataProvider questionsDataProvider
     *
     * @param $count
     */
    public function test_questions_listed_as_we_expected($count)
    {
        $questions = Question::factory($count)->create(['user_id' => $this->user->id])->map(function ($question) {
            return $question->only(['id', 'body', 'answer']);
        });

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::ListQuestions, QAStatesEnum::mainMenu())
            ->expectsTable(['ID', 'Question', 'Answer'], $questions->toArray());
    }

    public function questionsDataProvider(): array
    {
        return [
            ['count' => 6],
            ['count' => 1],
            ['count' => 2],
            ['count' => 4],
        ];
    }
}
