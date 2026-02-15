<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($notes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $note = Note::create(array_merge($validated, [
            'user_id' => Auth::id(),
            'preview' => \Illuminate\Support\Str::limit(strip_tags($validated['content'] ?? ''), 100),
        ]));

        return response()->json($note, 201);
    }

    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        if (isset($validated['content'])) {
            $validated['preview'] = \Illuminate\Support\Str::limit(strip_tags($validated['content']), 100);
        }

        $note->update($validated);
        return response()->json($note);
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(['message' => 'Note deleted']);
    }
}
