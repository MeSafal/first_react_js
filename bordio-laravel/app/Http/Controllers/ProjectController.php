<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * List all projects
     */
    public function index()
    {
        $projects = Project::with(['team', 'members', 'tasks'])->get();
        return view('viso.projects.index', compact('projects'));
    }

    /**
     * Store project (Blade form POST)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'team_id' => $validated['team_id'] ?? null,
            'user_id' => auth()->id(),
        ]);

        // Add members
        $memberIds = $validated['member_ids'] ?? [auth()->id()];
        if (!in_array(auth()->id(), $memberIds)) {
            $memberIds[] = auth()->id();
        }
        $project->members()->sync($memberIds);

        if ($request->expectsJson()) {
            return response()->json($project->load(['team', 'members']), 201);
        }

        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully');
    }

    /**
     * Update project
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'status' => 'sometimes|in:active,archived',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $memberIds = $validated['member_ids'] ?? null;
        unset($validated['member_ids']);

        $project->update($validated);

        if ($memberIds !== null) {
            $project->members()->sync($memberIds);
        }

        if ($request->expectsJson()) {
            return response()->json($project->load(['team', 'members']));
        }

        return back()->with('success', 'Project updated');
    }

    /**
     * Delete project
     */
    public function destroy(Request $request, Project $project)
    {
        $project->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Project deleted']);
        }

        return redirect()->route('my-work')->with('success', 'Project deleted');
    }

    // ── JSON API endpoints ──
    public function apiIndex()
    {
        return response()->json(Project::with(['team', 'members', 'tasks'])->get());
    }

    public function apiStore(Request $request)
    {
        return $this->store($request);
    }

    public function apiUpdate(Request $request, Project $project)
    {
        return $this->update($request, $project);
    }

    public function apiDestroy(Request $request, Project $project)
    {
        return $this->destroy($request, $project);
    }

    /**
     * Remove member from project and handle task transitions
     */
    public function removeMember(Project $project, User $user)
    {
        // 1. Remove from project
        $project->members()->detach($user->id);

        // 2. Cascade to tasks: remove from assignees
        $tasks = $project->tasks()->get();

        foreach ($tasks as $task) {
            // Remove user from task assignees
            $task->assignees()->detach($user->id);

            // 3. If no assignees left, assign to project owner (creator)
            if ($task->assignees()->count() === 0 && $project->user_id) {
                $task->assignees()->attach($project->user_id);
            }
        }

        return response()->json([
            'message' => 'Member removed successfully',
            'task_reassigned' => true
        ]);
    }
    /**
     * Add new members to the project
     */
    public function addMembers(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $project->members()->attach($validated['user_ids']);

        return response()->json([
            'message' => count($validated['user_ids']) . ' members added successfully'
        ]);
    }
}
