<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * User Management page
     */
    public function index()
    {
        $users = User::all();
        $teams = Team::with('members')->get();
        return view('viso.users', compact('users', 'teams'));
    }

    /**
     * Store a new user (temp member)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
            'team_ids' => 'nullable|array',
            'team_ids.*' => 'exists:teams,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'] ?? 'Member',
            'password' => Hash::make($validated['password'] ?? 'password'),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($validated['name']) . '&background=3b82f6&color=fff',
        ]);

        $user->assignRole('user');

        if (!empty($validated['team_ids'])) {
            foreach ($validated['team_ids'] as $teamId) {
                Team::find($teamId)?->members()->syncWithoutDetaching([$user->id]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json($user, 201);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        if (isset($validated['name'])) {
            $user->update(['avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($validated['name']) . '&background=3b82f6&color=fff']);
        }

        if ($request->expectsJson()) {
            return response()->json($user);
        }

        return redirect()->route('users.index')->with('success', 'User updated');
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cannot delete yourself'], 403);
            }
            return back()->with('error', 'Cannot delete yourself');
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'User deleted']);
        }

        return redirect()->route('users.index')->with('success', 'User deleted');
    }

    /**
     * JSON endpoint to list users
     */
    public function apiList()
    {
        return response()->json(User::all());
    }
}
