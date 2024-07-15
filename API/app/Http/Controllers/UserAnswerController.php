<?php

namespace App\Http\Controllers;

use App\Models\UserAnswer;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class UserAnswerController extends Controller
{
    public function index()
    {
        return UserAnswer::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'quiz_question_id' => 'required|exists:quiz_questions,id',
            'selected_option' => 'required|integer',
        ]);

        $question = QuizQuestion::findOrFail($data['quiz_question_id']);
        $isCorrect = $question->correct_answer === $data['selected_option'];
        $data['correct'] = $isCorrect;

        $userAnswer = UserAnswer::create($data);

        return response()->json([
            'message' => 'Answer stored successfully',
            'correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
        ], 201);
    }

    public function show($id)
    {
        return UserAnswer::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $userAnswer = UserAnswer::findOrFail($id);
        $userAnswer->update($request->all());
        return response()->json($userAnswer, 200);
    }

    public function destroy($id)
    {
        UserAnswer::destroy($id);
        return response()->json(null, 204);
    }
}
