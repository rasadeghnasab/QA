<?php

namespace Tests\Feature\QACommand;

use App\Models\Question;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class StatsTest extends QATestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider statsDataProvider
     *
     * @param int $total
     * @param string $answered_percentage
     * @param string $correct_percentage
     * @param array $statuses
     */
    public function test_stats_table_should_be_as_we_expected(int $total, string $answered_percentage, string $correct_percentage, array $statuses): void
    {
        foreach ($statuses as $status => $count) {
            Question::factory($count)->create(['user_id' => $this->user->id, 'status' => $status]);
        }

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Stats, QAStatesEnum::mainMenu())
            ->expectsTable(['Title', 'Value'], [
                ['Total', $total],
                ['Answered', $answered_percentage],
                ['Correct', $correct_percentage]
            ]);
    }

    public function statsDataProvider(): array
    {
        return [
            [
                'total' => 0,
                'answered_percentage' => '%0',
                'correct_percentage' => '%0',
                'statuses' => [
                    'Not answered' => 0,
                    'Correct' => 0,
                    'Incorrect' => 0,
                ]
            ],
            [
                'total' => 20,
                'answered_percentage' => '%50',
                'correct_percentage' => '%25',
                'statuses' => [
                    'Not answered' => 10,
                    'Correct' => 5,
                    'Incorrect' => 5,
                ]
            ],
            [
                'total' => 20,
                'answered_percentage' => '%0',
                'correct_percentage' => '%0',
                'statuses' => [
                    'Not answered' => 20,
                    'Correct' => 0,
                    'Incorrect' => 0,
                ]
            ],
            [
                'total' => 20,
                'answered_percentage' => '%100',
                'correct_percentage' => '%0',
                'statuses' => [
                    'Not answered' => 0,
                    'Correct' => 0,
                    'Incorrect' => 20,
                ]
            ],
            [
                'total' => 20,
                'answered_percentage' => '%100',
                'correct_percentage' => '%100',
                'statuses' => [
                    'Not answered' => 0,
                    'Correct' => 20,
                    'Incorrect' => 0,
                ]
            ],
        ];
    }
}
