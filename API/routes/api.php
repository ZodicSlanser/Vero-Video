<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('videos', VideoController::class);
Route::apiResource('quiz-questions', QuizQuestionController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('user-answers', UserAnswerController::class);
