@extends('layouts.app')
@section('title', 'My Work')

@section('content')
<div class="container-fluid py-4" style="max-width:1000px">
    {{-- Hero Header --}}
    <header class="mb-5 viso-slide-up">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">
                    @php
                        $hour = now()->hour;
                        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                    @endphp
                    {{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}! 👋
                </h1>
                <p class="text-muted mt-1 mb-0">
                    @if($activeTasks->count() > 0)
                        You have <strong class="text-dark">{{ $activeTasks->count() }} active tasks</strong>.
                        @if($overdueTasks->count() > 0)
                            <span class="text-danger fw-medium">{{ $overdueTasks->count() }} overdue!</span>
                        @else
                            Keep up the great work! 🚀
                        @endif
                    @else
                        All caught up! Time to plan something new. ✨
                    @endif
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#quickAddTaskModal">
                    <i class="icon-plus" style="font-size:18px"></i>
                    <span class="d-none d-md-inline">New Task</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3">
            <div class="viso-metric-card viso-metric-danger">
                <div class="viso-metric-icon"><i class="icon-alert-circle"></i></div>
                <div class="viso-metric-value">{{ $overdueTasks->count() }}</div>
                <div class="viso-metric-label">Overdue</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="viso-metric-card viso-metric-primary">
                <div class="viso-metric-icon"><i class="icon-clock"></i></div>
                <div class="viso-metric-value">{{ $dueTodayTasks->count() }}</div>
                <div class="viso-metric-label">Due Today</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="viso-metric-card viso-metric-warning">
                <div class="viso-metric-icon"><i class="icon-calendar"></i></div>
                <div class="viso-metric-value">{{ $upcomingTasks->count() }}</div>
                <div class="viso-metric-label">Upcoming</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="viso-metric-card viso-metric-success">
                <div class="viso-metric-icon"><i class="icon-check-circle-2"></i></div>
                <div class="viso-metric-value">{{ $completedTasks->count() }}</div>
                <div class="viso-metric-label">Completed</div>
            </div>
        </div>
    </div>

    {{-- Progress Ring --}}
    @php
        $total = $activeTasks->count() + $completedTasks->count();
        $pct = $total > 0 ? round(($completedTasks->count() / $total) * 100) : 0;
    @endphp
    <div class="card mb-5 border-0 bg-gradient-primary text-white">
        <div class="card-body p-4 d-flex align-items-center gap-4">
            <div class="viso-progress-ring" style="--pct: {{ $pct }}">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="34" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="6"/>
                    <circle cx="40" cy="40" r="34" fill="none" stroke="#fff" stroke-width="6"
                            stroke-dasharray="{{ 2 * 3.14159 * 34 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 34 * (1 - $pct / 100) }}"
                            stroke-linecap="round" transform="rotate(-90 40 40)"/>
                </svg>
                <span class="viso-progress-ring-text">{{ $pct }}%</span>
            </div>
            <div>
                <h3 class="fw-bold mb-1">Overall Progress</h3>
                <p class="mb-0 opacity-75">{{ $completedTasks->count() }} of {{ $total }} tasks completed this cycle</p>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column gap-4">
        {{-- Overdue --}}
        @if($overdueTasks->count())
        <section class="viso-fade-in">
            <h2 class="h5 fw-bold text-danger mb-3 d-flex align-items-center gap-2">
                <i class="icon-alert-triangle" style="font-size:18px"></i>
                Overdue <span class="badge bg-danger rounded-pill small">{{ $overdueTasks->count() }}</span>
            </h2>
            <div class="d-flex flex-column gap-2">
                @foreach($overdueTasks as $task)
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-grow-1">@include('components.task-row', ['task' => $task])</div>
                        @include('components.action-menu', ['taskId' => $task->id])
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Due Today --}}
        <section class="viso-fade-in">
            <h2 class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="icon-sun text-warning" style="font-size:18px"></i>
                Due Today <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill small">{{ $dueTodayTasks->count() }}</span>
            </h2>
            <div class="d-flex flex-column gap-2">
                @forelse($dueTodayTasks as $task)
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-grow-1">@include('components.task-row', ['task' => $task])</div>
                        @include('components.action-menu', ['taskId' => $task->id])
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="icon-check-circle-2 d-block mb-2" style="font-size:32px;opacity:0.3"></i>
                        <span class="small fst-italic">No tasks due today. 🎉</span>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Upcoming --}}
        <section class="viso-fade-in">
            <h2 class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="icon-arrow-right text-primary" style="font-size:18px"></i>
                Upcoming <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill small">{{ $upcomingTasks->count() }}</span>
            </h2>
            <div class="d-flex flex-column gap-2">
                @foreach($upcomingTasks as $task)
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-grow-1">@include('components.task-row', ['task' => $task])</div>
                        @include('components.action-menu', ['taskId' => $task->id])
                    </div>
                @endforeach
                @if($upcomingTasks->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <span class="small fst-italic">No upcoming tasks. Time to plan ahead!</span>
                    </div>
                @endif
            </div>
        </section>

        {{-- Quick Add Task --}}
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded border cursor-pointer hover-shadow transition-all"
             onclick="document.getElementById('inlineNewTask').focus()">
            <i class="icon-plus text-primary" style="font-size:20px"></i>
            <input id="inlineNewTask" type="text" placeholder="Quick add a task... press Enter"
                   class="form-control-plaintext border-0 shadow-none p-0 flex-grow-1 small"
                   onkeydown="if(event.key==='Enter'&&this.value.trim()){VisoApp.addTask(this.value);this.value='';}">
            <span class="badge bg-light text-muted border small">⏎ Enter</span>
        </div>

        {{-- Completed (collapsible) --}}
        @if($completedTasks->count())
        <section>
            <button class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-2 mb-3 small fw-medium"
                    data-bs-toggle="collapse" data-bs-target="#completedSection">
                <i class="icon-chevron-down" style="font-size:16px"></i>
                Completed Tasks ({{ $completedTasks->count() }})
            </button>
            <div class="collapse ps-3 border-start border-2 border-success border-opacity-25" id="completedSection">
                <div class="d-flex flex-column gap-2">
                    @foreach($completedTasks as $task)
                        @include('components.task-row', ['task' => $task])
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    </div>
</div>

{{-- Quick Add Task Modal --}}
<div class="modal fade" id="quickAddTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="icon-zap text-warning me-2" style="font-size:20px"></i>
                    Quick Add Task
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Title</label>
                    <input type="text" id="quickTaskTitle" class="form-control" placeholder="What needs to be done?" autofocus>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Project</label>
                        <select id="quickTaskProject" class="form-select form-select-sm">
                            <option value="">Personal (No project)</option>
                            @php $projects = \App\Models\Project::all(); @endphp
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Priority</label>
                        <select id="quickTaskPriority" class="form-select form-select-sm">
                            <option value="Normal" selected>Normal</option>
                            <option value="Low">Low</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-0">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Due Date</label>
                        <input type="date" id="quickTaskDue" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Assign To</label>
                        <select id="quickTaskAssignee" class="form-select form-select-sm" multiple>
                            @php $users = \App\Models\User::all(); @endphp
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $u->id === auth()->id() ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="VisoApp.quickAddTask()">
                    <i class="icon-plus me-1" style="font-size:16px"></i> Create Task
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
