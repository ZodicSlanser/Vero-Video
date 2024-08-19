<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAnswerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::apiResource('videos', VideoController::class);
Route::apiResource('quiz-questions', QuizQuestionController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('user-answers', UserAnswerController::class);

Route::get('/stream/videos/{filename}', function ($filename) {
    $path = public_path('videos/' . $filename);
    if (!File::exists($path)) {
        abort(400);
    }
    return response()->file($path);
});
