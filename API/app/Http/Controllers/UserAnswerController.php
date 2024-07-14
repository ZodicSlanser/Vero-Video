<?php

namespace App\Http\Controllers;

use App\Models\UserAnswer;
use Illuminate\Http\Request;

class UserAnswerController extends Controller
{
    public function index()
    {
        return UserAnswer::all();
    }

    public function store(Request $request)
    {
        $userAnswer = UserAnswer::create($request->all());
        return response()->json($userAnswer, 201);
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
