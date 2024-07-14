<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        return Video::all();
    }

    public function store(Request $request)
    {
        $video = Video::create($request->all());
        return response()->json($video, 201);
    }

    public function show($id)
    {
        return Video::with('quizQuestions')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $video->update($request->all());
        return response()->json($video, 200);
    }

    public function destroy($id)
    {
        Video::destroy($id);
        return response()->json(null, 204);
    }
}

