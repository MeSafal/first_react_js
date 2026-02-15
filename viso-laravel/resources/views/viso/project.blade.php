@extends('layouts.app')
@section('title', $project->name)

@section('content')
<div class="container-fluid py-4" style="max-width:1000px">
    {{-- Project Header --}}
    <header class="mb-5 viso-slide-up">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                    <a href="{{ route('my-work') }}" class="text-decoration-none text-muted hover-text-primary">
                        <i class="icon-home" style="font-size:14px"></i>
                    </a>
                    <i class="icon-chevron-right" style="font-size:12px"></i>
                    @if($project->team)
                        <span>{{ $project->team->name }}</span>
                        <i class="icon-chevron-right" style="font-size:12px"></i>
                    @endif
                    <span class="text-dark fw-medium">{{ $project->name }}</span>
                </div>
                <h1 class="h2 fw-bold text-dark mb-2">{{ $project->name }}</h1>
                <div class="d-flex align-items-center gap-3 flex-wrap text-muted small">
                    {{-- Members --}}
                    <div class="d-flex align-items-center gap-2">
                        <div class="viso-avatar-stack">
                            @foreach($project->members->take(5) as $member)
                                <img src="{{ $member->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&size=28&background=3b82f6&color=fff' }}"
                                     alt="{{ $member->name }}" class="rounded-circle border border-2 border-white" width="28" height="28"
                                     title="{{ $member->name }}">
                            @endforeach
                        </div>
                        <span>{{ $project->members->count() }} members</span>
                    </div>
                    <span class="text-muted">•</span>
                    <span>{{ $activeTasks->count() + $completedTasks->count() }} tasks</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addProjectTaskModal">
                    <i class="icon-plus" style="font-size:16px"></i> New Task
                </button>
                <div class="dropdown">
                    <button class="btn btn-light border" data-bs-toggle="dropdown">
                        <i class="icon-more-horizontal" style="font-size:16px"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item small text-danger" href="#"
                               onclick="event.preventDefault(); if(confirm('Delete this project and all its tasks?')) document.getElementById('deleteProjectForm').submit()">
                            <i class="icon-trash-2 me-2" style="font-size:14px"></i> Delete Project
                        </a></li>
                    </ul>
                </div>
                <form id="deleteProjectForm" method="POST" action="{{ route('projects.destroy', $project) }}" style="display:none">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>
    </header>

    {{-- Progress Bar --}}
    @php
        $total = $activeTasks->count() + $completedTasks->count();
        $pct = $total > 0 ? round(($completedTasks->count() / $total) * 100) : 0;
    @endphp
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-bold text-dark">Progress</span>
                    <span class="small fw-bold text-primary">{{ $pct }}%</span>
                </div>
                <div class="progress" style="height:6px">
                    <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            <div class="d-flex gap-4 text-center">
                <div>
                    <div class="fw-bold text-dark">{{ $activeTasks->count() }}</div>
                    <div class="text-muted fs-10 text-uppercase">Active</div>
                </div>
                <div>
                    <div class="fw-bold text-success">{{ $completedTasks->count() }}</div>
                    <div class="text-muted fs-10 text-uppercase">Done</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Tasks --}}
    <section class="mb-4">
        <h2 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i class="icon-circle text-primary" style="font-size:12px"></i>
            Active Tasks <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $activeTasks->count() }}</span>
        </h2>
        <div class="d-flex flex-column gap-2">
            @foreach($activeTasks as $task)
                <div class="d-flex align-items-center gap-2">
                    <div class="flex-grow-1">@include('components.task-row', ['task' => $task])</div>
                    @include('components.action-menu', ['taskId' => $task->id])
                </div>
            @endforeach
            @if($activeTasks->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="icon-inbox d-block mb-2" style="font-size:40px;opacity:0.2"></i>
                    <p class="small fst-italic mb-2">No active tasks yet.</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProjectTaskModal">
                        <i class="icon-plus me-1" style="font-size:14px"></i> Create First Task
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- Quick add inline --}}
    <div class="d-flex align-items-center gap-3 p-3 bg-white rounded border mb-4 cursor-pointer hover-shadow transition-all"
         onclick="document.getElementById('projectInlineTask').focus()">
        <i class="icon-plus text-primary" style="font-size:18px"></i>
        <input id="projectInlineTask" type="text" placeholder="Quick add task to {{ $project->name }}..."
               class="form-control-plaintext border-0 shadow-none p-0 flex-grow-1 small"
               onkeydown="if(event.key==='Enter'&&this.value.trim()){VisoApp.addTask(this.value, {{ $project->id }});this.value='';}">
        <span class="badge bg-light text-muted border small">⏎</span>
    </div>

    {{-- Completed --}}
    @if($completedTasks->count())
    <section>
        <button class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-2 mb-3 small fw-medium"
                data-bs-toggle="collapse" data-bs-target="#completedProjectTasks">
            <i class="icon-chevron-down" style="font-size:14px"></i>
            Completed ({{ $completedTasks->count() }})
        </button>
        <div class="collapse border-start border-2 border-success border-opacity-25 ps-3" id="completedProjectTasks">
            <div class="d-flex flex-column gap-2">
                @foreach($completedTasks as $task)
                    @include('components.task-row', ['task' => $task])
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>

{{-- Add Task to Project Modal --}}
<div class="modal fade" id="addProjectTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="icon-zap text-warning me-2" style="font-size:20px"></i>
                    New Task in {{ $project->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Title</label>
                    <input type="text" id="projectTaskTitle" class="form-control" placeholder="What needs to be done?" autofocus>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Priority</label>
                        <select id="projectTaskPriority" class="form-select form-select-sm">
                            <option value="Normal" selected>Normal</option>
                            <option value="Low">Low</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Due Date</label>
                        <input type="date" id="projectTaskDue" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="VisoApp.addProjectTask({{ $project->id }})">
                    <i class="icon-plus me-1" style="font-size:16px"></i> Create
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => VisoApp.toast('{{ session('success') }}'));</script>
@endif
@endsection
