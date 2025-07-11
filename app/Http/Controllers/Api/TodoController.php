<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Todo::query();

        // Filter by completion status
        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query->completed();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $todos = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $todos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date',
        ]);

        $todo = Todo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Todo created successfully',
            'data' => $todo
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $todo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'sometimes|boolean',
            'priority' => ['sometimes', 'required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date',
        ]);

        $todo->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Todo updated successfully',
            'data' => $todo->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully'
        ]);
    }

    /**
     * Toggle the completion status of a todo.
     */
    public function toggle(Todo $todo): JsonResponse
    {
        $todo->update(['completed' => !$todo->completed]);

        return response()->json([
            'success' => true,
            'message' => 'Todo status updated successfully',
            'data' => $todo->fresh()
        ]);
    }
}
