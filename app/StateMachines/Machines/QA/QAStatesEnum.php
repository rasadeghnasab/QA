<?php

namespace App\StateMachines\Machines\QA;

use BenSampo\Enum\Enum;

final class QAStatesEnum extends Enum
{
    const Authenticate = 'Authenticate';
    const MainMenu = 'MainMenu';
    const AddQuestion = 'Create a question';
    const ListQuestions = 'List questions';
    const Practice = 'Practice';
    const Stats = 'Stats';
    const Reset = 'Reset';
    const Exit = 'Exit';

    public static function mainMenu()
    {
        return [
            self::AddQuestion,
            self::ListQuestions,
            self::Practice,
            self::Stats,
            self::Reset,
            self::Exit,
        ];
    }
}
