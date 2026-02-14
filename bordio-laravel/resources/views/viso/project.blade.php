@extends('layouts.app')
@section('title', $project->name)

@section('content')
<div class="container-fluid py-4" style="max-width:1000px">
    {{-- Header --}}
    <header class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="small text-muted mb-1 text-uppercase fw-bold ls-1">
                Project / {{ $project->team->name ?? 'General' }}
            </div>
            <h1 class="h2 fw-bold text-dark mb-0">{{ $project->name }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            {{-- Members --}}
            <div class="viso-avatar-stack me-2">
                @foreach($project->members->take(3) as $member)
                    <img src="{{ $member->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&size=32&background=3b82f6&color=fff' }}"
                         alt="{{ $member->name }}" class="rounded-circle border border-white" width="32" height="32" title="{{ $member->name }}">
                @endforeach
                @if($project->members->count() > 3)
                    <span class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center small fw-bold text-secondary"
                          style="width:32px;height:32px">+{{ $project->members->count() - 3 }}</span>
                @endif
            </div>
            <button class="btn btn-light btn-sm d-flex align-items-center gap-2">
                <i class="icon-filter" style="font-size:16px"></i> Filter
            </button>
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                    onclick="document.getElementById('projectNewTask').focus()">
                <i class="icon-plus" style="font-size:16px"></i> Add Task
            </button>
        </div>
    </header>

    {{-- Active Tasks --}}
    <section class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 fw-bold text-dark d-flex align-items-center gap-2 m-0">
                Active Tasks
                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill small">{{ $activeTasks->count() }}</span>
            </h2>
        </div>

        <div class="d-flex flex-column gap-2">
            @foreach($activeTasks as $task)
                <div class="d-flex align-items-center gap-2">
                    <div class="flex-grow-1">@include('components.task-row', ['task' => $task])</div>
                    @include('components.action-menu', ['taskId' => $task->id])
                </div>
            @endforeach
        </div>

        {{-- Inline Add --}}
        <div class="mt-2 d-flex align-items-center gap-3 p-3 text-muted bg-white rounded hover-shadow cursor-pointer"
             onclick="document.getElementById('projectNewTask').focus()">
            <i class="icon-plus text-muted" style="font-size:20px"></i>
            <input id="projectNewTask" type="text" placeholder="Add a new task..."
                   class="form-control-plaintext border-0 shadow-none p-0 flex-grow-1 small"
                   onkeydown="if(event.key==='Enter'&&this.value.trim()) VisoApp.addTask(this.value, {{ $project->id }})">
            <span class="badge bg-light text-muted border">Enter</span>
        </div>
    </section>

    {{-- Completed Tasks --}}
    @if($completedTasks->count())
    <section>
        <button class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-2 mb-3 small fw-medium"
                data-bs-toggle="collapse" data-bs-target="#projCompleted">
            <i class="icon-chevron-down" style="font-size:16px"></i>
            Completed Tasks ({{ $completedTasks->count() }})
        </button>
        <div class="collapse ps-3 border-start border-2 border-light" id="projCompleted">
            <div class="d-flex flex-column gap-2">
                @foreach($completedTasks as $task)
                    @include('components.task-row', ['task' => $task])
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>
@endsection
