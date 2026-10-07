<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\QuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/topics', TopicController::class);
Route::apiResource('/lessons', LessonController::class);
Route::apiResource('/questions', QuestionController::class);
