<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Quiz::with('questions')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string:255',
            'type' => 'required|string:255|in:lesson_summary,mixed_review',
            'question_ids' => 'array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $quiz = Quiz::create(Arr::except($validated, 'question_ids'));
        $quiz->questions()->sync($validated['question_ids'] ?? []);

        return response()->json($quiz->load('questions'), 201);
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
            'type' => 'in:lesson_summary,mixed_review',
            'question_ids' => 'array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $quiz->update(Arr::except($validated, 'question_ids'));

        if ($request->has('question_ids')) {
            $quiz->questions()->sync($validated['question_ids']);
        }

        return $quiz->load('questions');
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
