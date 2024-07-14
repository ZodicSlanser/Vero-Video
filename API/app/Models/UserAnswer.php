<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'quiz_question_id', 'selected_option', 'correct'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class);
    }
}
