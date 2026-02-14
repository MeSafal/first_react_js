<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Subtask;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with(['subtasks', 'assignees', 'chatMessages.user', 'project'])
            ->when($request->project_id, fn($q, $pid) => $q->where('project_id', $pid))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'status' => 'in:Todo,In Progress,Under Review,Completed,Scheduled',
            'priority' => 'in:Low,Normal,High,Urgent',
            'due_date' => 'nullable|date',
            'time_estimate' => 'nullable|integer|min:0',
            'recurrence' => 'in:none,daily,weekly,monthly',
            'tags' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        $task = Task::create(array_merge([
            'status' => 'Todo',
            'priority' => 'Normal',
            'time_estimate' => 30,
            'recurrence' => 'none',
        ], $validated));

        // Assign current user
        $task->assignees()->attach(Auth::id());

        return response()->json($task->load(['subtasks', 'assignees', 'project']), 201);
    }

    public function show(Task $task)
    {
        return response()->json(
            $task->load(['subtasks', 'assignees', 'chatMessages.user', 'project'])
        );
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:Todo,In Progress,Under Review,Completed,Scheduled',
            'priority' => 'sometimes|in:Low,Normal,High,Urgent',
            'due_date' => 'nullable|date',
            'time_estimate' => 'sometimes|integer|min:0',
            'recurrence' => 'sometimes|in:none,daily,weekly,monthly',
            'tags' => 'nullable|array',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $task->update($validated);

        return response()->json($task->load(['subtasks', 'assignees', 'project']));
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    public function duplicate(Task $task)
    {
        $newTask = $task->replicate();
        $newTask->title = $task->title . ' (Copy)';
        $newTask->status = 'Todo';
        $newTask->save();

        // Copy subtasks
        foreach ($task->subtasks as $subtask) {
            $newTask->subtasks()->create([
                'title' => $subtask->title,
                'completed' => false,
            ]);
        }

        // Copy assignees
        $newTask->assignees()->sync($task->assignees->pluck('id'));

        return response()->json($newTask->load(['subtasks', 'assignees', 'project']), 201);
    }

    // Subtask endpoints
    public function addSubtask(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $subtask = $task->subtasks()->create([
            'title' => $validated['title'],
            'completed' => false,
        ]);

        return response()->json($subtask, 201);
    }

    public function toggleSubtask(Task $task, Subtask $subtask)
    {
        $subtask->update(['completed' => !$subtask->completed]);
        return response()->json($subtask);
    }

    public function deleteSubtask(Task $task, Subtask $subtask)
    {
        $subtask->delete();
        return response()->json(['message' => 'Subtask deleted']);
    }

    // Chat endpoints
    public function sendMessage(Request $request, Task $task)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $message = $task->chatMessages()->create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
        ]);

        return response()->json($message->load('user'), 201);
    }
}
