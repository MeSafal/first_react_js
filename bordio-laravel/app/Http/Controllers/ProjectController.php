<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
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

        return redirect()->route('project.view', $project)->with('success', 'Project created successfully');
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
}
