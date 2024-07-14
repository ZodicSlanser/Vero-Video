<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['video_id', 'seconds', 'question', 'options', 'correct_answer', 'type'];

    protected $casts = [
        'options' => 'array'
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
