<?php

namespace App\Models;

use App\Enums\PracticeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

class QuestionUser extends Pivot
{
    use HasFactory;

    protected $fillable = ['question_id', 'user_id', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    public function scopeScoreBoard($query)
    {
        return $query->select(
            'users.name',
            'users.email',
            DB::raw('COUNT(question_user.user_id) as total_answered'),
            DB::raw(sprintf('COUNT(CASE WHEN status = %d THEN status END) as incorrect', 2)),
            DB::raw(sprintf('COUNT(CASE WHEN status = %d THEN status END) as correct', 1))
        )
            ->join('users', 'question_user.user_id', 'users.id')
            ->groupBy('user_id')
            ->orderByDesc('correct')
            ->orderByDesc('total_answered');
    }

    public function getStatusNameAttribute(): string
    {
        return PracticeStatusEnum::getDescription($this->attributes['status']);
    }
}
