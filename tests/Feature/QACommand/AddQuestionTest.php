<?php

namespace Tests\Feature\QACommand;

use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class AddQuestionTest extends QATestCase
{
    use RefreshDatabase;

    public function test_a_question_can_be_added_successfully(): void
    {
        $this->assertEquals(0, $this->user->questions()->count());

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::AddQuestion, QAStatesEnum::mainMenu())
            ->expectsQuestion('Enter the question body please', 'question body')
            ->expectsQuestion('Enter the answer please', 'question answer')
            ->expectsOutput('The question has been added successfully.')
            ->expectsConfirmation('Add another one?', 'no')
            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu());

        $this->assertEquals(1, $this->user->questions()->count());
    }

    public function test_question_body_and_answer_should_be_valid(): void
    {
        $empty = '';
        $short = 'a';

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::AddQuestion, QAStatesEnum::mainMenu())
            // data is required
            ->expectsQuestion('Enter the question body please', $empty)
            ->expectsQuestion('Enter the answer please', $empty)
            ->expectsOutput('The question field is required.')
            ->expectsOutput('The answer field is required.')

            // question and answer should be more than 2 character
            ->expectsQuestion('Enter the question body please', $short)
            ->expectsQuestion('Enter the answer please', $short)
            ->expectsOutput('The question must be at least 2 characters.')
            ->expectsOutput('The answer must be at least 2 characters.')

            // valid data
            ->expectsQuestion('Enter the question body please', 'question body')
            ->expectsQuestion('Enter the answer please', 'question answer')

            // successfully added
            ->expectsOutput('The question has been added successfully.')
            ->expectsConfirmation('Add another one?', 'no');
    }
}
