<?php

namespace App\Http\Controllers;

use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    public function index()
    {
        return QuizQuestion::all();
    }

    public function store(Request $request)
    {
        $quizQuestion = QuizQuestion::create($request->all());
        return response()->json($quizQuestion, 201);
    }

    public function show($id)
    {
        return QuizQuestion::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $quizQuestion = QuizQuestion::findOrFail($id);
        $quizQuestion->update($request->all());
        return response()->json($quizQuestion, 200);
    }

    public function destroy($id)
    {
        QuizQuestion::destroy($id);
        return response()->json(null, 204);
    }
}
