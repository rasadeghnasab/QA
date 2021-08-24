<?php

namespace App\Models;

use App\Enums\PracticeStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class QuestionUser extends Pivot
{
    use HasFactory;

    protected $fillable = ['question_id', 'user_id', 'status'];

    public function scopeCorrect(Builder $query)
    {
        return $query->where('status', '=', PracticeStatusEnum::Correct);
    }

    public function scopeIncorrect(Builder $query)
    {
        return $query->where('status', PracticeStatusEnum::Incorrect);
    }

    public function scopeNotAnswered(Builder $query)
    {
        return $query->where('status', PracticeStatusEnum::NotAnswered);
    }
}
