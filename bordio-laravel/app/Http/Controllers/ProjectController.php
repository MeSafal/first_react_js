<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['team', 'members', 'tasks'])->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $project = Project::create($validated);

        return response()->json($project->load(['team', 'members']), 201);
    }

    public function show(Project $project)
    {
        return response()->json(
            $project->load(['team', 'members', 'tasks.subtasks', 'tasks.assignees'])
        );
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'status' => 'sometimes|in:active,archived',
        ]);

        $project->update($validated);

        return response()->json($project->load(['team', 'members']));
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }
}
