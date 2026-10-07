<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Quiz::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string:255',
            'type' => 'required|string:255|in:lesson_summary,mixed_review',
        ]);

        $quiz = Quiz::create($validated);

        return response()->json($quiz, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        return $quiz;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'type' => 'string|max:255|in:lesson_summary,mixed_review',

        ]);

        $quiz->update($validated);

        return $quiz;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return response()->json(null, 204);
    }
}
