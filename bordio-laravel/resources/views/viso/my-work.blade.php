@extends('layouts.app')
@section('title', 'My Work')

@section('content')
<div class="container-fluid py-4" style="max-width:1000px">
    {{-- Header --}}
    <header class="mb-5">
        <h1 class="h2 fw-bold text-dark">Good morning, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
        <p class="text-muted mt-1">You have {{ $activeTasks->count() }} tasks to complete. Let's get to work.</p>
    </header>

    {{-- Stats Row --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            @include('components.stat-card', ['icon' => 'alert-circle', 'color' => 'danger', 'value' => $overdueTasks->count(), 'label' => 'Overdue'])
        </div>
        <div class="col-md-4">
            @include('components.stat-card', ['icon' => 'clock', 'color' => 'primary', 'value' => $dueTodayTasks->count(), 'label' => 'Due Today'])
        </div>
        <div class="col-md-4">
            @include('components.stat-card', ['icon' => 'check-circle-2', 'color' => 'success', 'value' => $completedTasks->count(), 'label' => 'Completed'])
        </div>
    </div>

    <div class="d-flex flex-column gap-5">
        {{-- Overdue --}}
        @if($overdueTasks->count())
        <section>
            <h2 class="h5 fw-bold text-danger mb-3 d-flex align-items-center gap-2">
                Overdue <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill small">{{ $overdueTasks->count() }}</span>
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
        <section>
            <h2 class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                Due Today <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill small">{{ $dueTodayTasks->count() }}</span>
            </h2>
            <div class="d-flex flex-column gap-2">
                @forelse($dueTodayTasks as $task)
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-grow-1">@include('components.task-row', ['task' => $task])</div>
                        @include('components.action-menu', ['taskId' => $task->id])
                    </div>
                @empty
                    <div class="text-muted fst-italic small">No tasks due today. Great job!</div>
                @endforelse
            </div>
        </section>

        {{-- Upcoming --}}
        <section>
            <h2 class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                Upcoming <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill small">{{ $upcomingTasks->count() }}</span>
            </h2>
            <div class="d-flex flex-column gap-2">
                @foreach($upcomingTasks as $task)
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-grow-1">@include('components.task-row', ['task' => $task])</div>
                        @include('components.action-menu', ['taskId' => $task->id])
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Inline Add Task --}}
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded border cursor-pointer hover-shadow"
             onclick="document.getElementById('inlineNewTask').focus()">
            <i class="icon-plus text-muted" style="font-size:20px"></i>
            <input id="inlineNewTask" type="text" placeholder="Add a new task..."
                   class="form-control-plaintext border-0 shadow-none p-0 flex-grow-1 small"
                   onkeydown="if(event.key==='Enter'&&this.value.trim()) VisoApp.addTask(this.value)">
            <span class="badge bg-light text-muted border">Enter</span>
        </div>

        {{-- Completed (collapsible) --}}
        @if($completedTasks->count())
        <section>
            <button class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-2 mb-3 small fw-medium"
                    data-bs-toggle="collapse" data-bs-target="#completedSection">
                <i class="icon-chevron-down" style="font-size:16px"></i>
                Completed Tasks ({{ $completedTasks->count() }})
            </button>
            <div class="collapse ps-3 border-start border-2" id="completedSection">
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
@endsection
