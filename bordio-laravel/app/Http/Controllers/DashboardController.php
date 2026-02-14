<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * My Work — personal task dashboard
     */
    public function myWork()
    {
        $user = Auth::user();

        $tasks = Task::with(['subtasks', 'assignees', 'project'])
            ->whereHas('assignees', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('created_at', 'desc')
            ->get();

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();

        $activeTasks = $tasks->where('status', '!=', 'Completed');
        $completedTasks = $tasks->where('status', 'Completed');

        $overdueTasks = $activeTasks->filter(
            fn($t) => $t->due_date && $t->due_date->lt($today)
        );

        $dueTodayTasks = $activeTasks->filter(
            fn($t) => $t->due_date && $t->due_date->isSameDay($today)
        );

        $upcomingTasks = $activeTasks->filter(
            fn($t) => !$t->due_date || $t->due_date->gt($today)
        );

        return view('viso.my-work', compact(
            'activeTasks', 'completedTasks', 'overdueTasks', 'dueTodayTasks', 'upcomingTasks'
        ));
    }

    /**
     * Project View — single project with tasks
     */
    public function projectView(Project $project)
    {
        $project->load(['team', 'members', 'tasks.subtasks', 'tasks.assignees']);

        $activeTasks = $project->tasks->where('status', '!=', 'Completed');
        $completedTasks = $project->tasks->where('status', 'Completed');

        return view('viso.project', compact('project', 'activeTasks', 'completedTasks'));
    }

    /**
     * Calendar — week view with draggable tasks
     */
    public function calendar(Request $request)
    {
        $weekStart = $request->query('week')
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekDays = collect(range(0, 6))->map(fn($i) => $weekStart->copy()->addDays($i));

        $tasks = Task::with(['subtasks', 'assignees', 'project'])->get();

        $waitingList = $tasks->whereNull('due_date')->where('status', '!=', 'Completed');

        return view('viso.calendar', compact('tasks', 'weekDays', 'weekStart', 'waitingList'));
    }

    /**
     * Kanban Board — columns by status
     */
    public function kanban()
    {
        $tasks = Task::with(['subtasks', 'assignees', 'project'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('viso.kanban', compact('tasks'));
    }

    /**
     * Notes — sidebar list + editor
     */
    public function notes()
    {
        $notes = Note::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('viso.notes', compact('notes'));
    }

    /**
     * Team Workload — grid of users × days
     */
    public function teamWorkload(Request $request)
    {
        $weekStart = $request->query('week')
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekDays = collect(range(0, 6))->map(fn($i) => $weekStart->copy()->addDays($i));

        $users = User::all();
        $tasks = Task::with(['subtasks', 'assignees'])
            ->whereNotNull('due_date')
            ->get();

        return view('viso.team-workload', compact('users', 'tasks', 'weekDays', 'weekStart'));
    }
}
