<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['path','show_answers'];
    protected $casts = [
        'path' => 'string',
        'show_answers' => 'boolean'
    ];

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
