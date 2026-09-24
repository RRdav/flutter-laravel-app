<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     * ALL
     */
    public function index()
    {
        // This returns all topics
        return Topic::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request body
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // create the topic
        $topic = Topic::create($validated);

        return response()->json($topic, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        return $topic;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topic $topic)
    {
        // validate the request
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $topic->update($validated);

        return $topic;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();

        return response()->json(null, 204);
    }
}
