<?php

namespace App\Models;

use App\Enums\PracticeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Gets all the questions + and determine if they already answered by the user or not?
     *
     * Note: this could go to a repository instead of the User model.
     */
    public function practiceQuestions()
    {
        return QuestionUser::select(
            'questions.id',
            'questions.body',
            DB::raw(sprintf("IFNULL(question_user.status, '%s') as `status`", PracticeStatusEnum::NotAnswered))
        )->rightJoin(
            'questions',
            'question_user.question_id',
            'questions.id',
        )
            ->where('question_user.user_id', '=', $this->id)
            ->orWhereNull('question_user.user_id');
    }


    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function practices()
    {
        return $this->hasMany(QuestionUser::class);
    }
}
