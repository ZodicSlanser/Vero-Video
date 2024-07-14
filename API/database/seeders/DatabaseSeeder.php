<?php

namespace Database\Seeders;
use App\Models\Video;
use App\Models\User;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $video = Video::create(['path' => 'videos/video.mp4']);
        $user = User::create(['name' => 'John Doe', 'info' => 'Sample user info']);

        QuizQuestion::create([
            'video_id' => $video->id,
            'seconds' => 10,
            'question' => "Which subject's project is Saif explaining?",
            'options' => ['Compiler', 'OS', 'System Analysis', 'Embedded Systems'],
            'correct_answer' => 0
        ]);

        QuizQuestion::create([
            'video_id' => $video->id,
            'seconds' => 17,
            'question' => "What was Saif's Academic Number?",
            'options' => ["200500", "260300", "200345", "200346"],
            'correct_answer' => 3
        ]);
    }
}
