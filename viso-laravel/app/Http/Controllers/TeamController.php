<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with(['members', 'projects'])->get();
        return response()->json($teams);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $team = Team::create($validated);
        return response()->json($team, 201);
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(['message' => 'Team deleted']);
    }
}
