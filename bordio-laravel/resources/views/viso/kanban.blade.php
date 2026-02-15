@extends('layouts.app')
@section('title', 'Kanban Board')

@section('content')
<div class="d-flex flex-column h-100 overflow-hidden">
    {{-- Header --}}
    <header class="flex-shrink-0 p-4 pb-0 viso-slide-up">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">Kanban Board</h1>
                <p class="text-muted mb-0">Drag and drop tasks to update status</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2 bg-white px-2 py-1 rounded border me-2">
                    <span class="small text-muted text-uppercase fw-bold fs-10">Show:</span>
                    <select class="form-select form-select-sm border-0 bg-transparent py-0 px-1 shadow-none fw-medium text-dark" style="width:auto">
                        <option>All Projects</option>
                        @foreach(\App\Models\Project::all() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#quickAddTaskModal">
                    <i class="icon-plus" style="font-size:18px"></i>
                    <span class="d-none d-md-inline">New Task</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Board --}}
    <div class="flex-grow-1 overflow-auto px-4 pb-4 viso-scroll">
        <div class="d-flex gap-4 h-100" style="width:max-content; min-width:100%">
            @php
                $columns = [
                    'Todo' => 'secondary',
                    'Scheduled' => 'info',
                    'In Progress' => 'primary',
                    'Under Review' => 'warning',
                    'Completed' => 'success'
                ];
            @endphp

            @foreach($columns as $status => $color)
                @php $colTasks = $tasks->where('status', $status); @endphp
                <div class="viso-kanban-col viso-fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s">
                    {{-- Column Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} rounded-pill p-1 d-flex align-items-center justify-content-center" style="width:24px;height:24px">
                                {{ $colTasks->count() }}
                            </span>
                            <h6 class="fw-bold text-dark mb-0">{{ $status }}</h6>
                        </div>
                        <button class="btn btn-sm btn-link text-muted p-0 hover-text-primary transition-all"
                                onclick="VisoApp.promptAddTask('{{ $status }}')" title="Add task to {{ $status }}">
                            <i class="icon-plus" style="font-size:18px"></i>
                        </button>
                    </div>

                    {{-- Drop Zone --}}
                    <div class="viso-kanban-drop"
                         data-kanban-status="{{ $status }}"
                         ondragover="event.preventDefault(); this.classList.add('drag-over')"
                         ondragleave="this.classList.remove('drag-over')"
                         ondrop="VisoApp.onKanbanDrop(event, '{{ $status }}')">
                        @foreach($colTasks as $task)
                            @include('components.kanban-card', ['task' => $task])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Reusing the Quick Add Task Modal from My Work --}}
{{-- Quick Add Task Modal --}}
@include('partials.quick-add-task-modal')
@endsection
