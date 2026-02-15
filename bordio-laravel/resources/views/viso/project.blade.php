@extends('layouts.app')
@section('title', $project->name)

@section('content')
<div class="h-100 overflow-auto bg-light bg-opacity-50 viso-scroll">
    {{-- Project Header --}}
    <header class="px-4 py-3 viso-slide-up bg-white border-bottom shadow-sm mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 text-muted tiny mb-1" style="font-size: 10px; letter-spacing: 0.5px; text-uppercase: uppercase;">
                    <a href="{{ route('my-work') }}" class="text-decoration-none text-muted hover-text-primary">
                        <i class="icon-home"></i>
                    </a>
                    <i class="icon-arrow-right" style="font-size:8px"></i>
                    @if($project->team)
                        <span>{{ $project->team->name }}</span>
                        <i class="icon-arrow-right" style="font-size:8px"></i>
                    @endif
                    <span class="text-primary fw-bold">{{ $project->name }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h4 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        {{ $project->name }}
                        <button class="btn btn-link p-0 text-muted hover-text-primary transition-all" 
                                style="font-size: 14px;"
                                data-bs-toggle="modal" data-bs-target="#editProjectModal" title="Edit Project">
                            <i class="icon-pencil"></i>
                        </button>
                    </h1>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 px-3 py-1 border rounded-pill bg-light" style="font-size: 12px;">
                    <i class="icon-layers text-primary"></i>
                    <span class="fw-bold text-dark">{{ $activeTasks->count() }}</span>
                    <span class="text-muted">Tasks</span>
                </div>
                <button class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3 py-1.5 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addProjectTaskModal">
                    <i class="icon-plus" style="font-size:14px"></i>
                    <span class="fw-bold">New Task</span>
                </button>
                <div class="dropdown">
                    <button class="btn btn-white border shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width:32px;height:32px" data-bs-toggle="dropdown">
                        <i class="icon-options-vertical" style="font-size:14px"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                        <li><a class="dropdown-item small py-2 text-danger" href="#"
                               onclick="event.preventDefault(); if(confirm('Delete project?')) document.getElementById('deleteProjectForm').submit()">
                            <i class="icon-trash me-2"></i> Delete Project
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    {{-- Body --}}
    <div class="px-4">
        {{-- Stat Row --}}
        <div class="row g-3 mb-5 viso-slide-shimmer">
            <div class="col-12 col-sm-6 col-md-3">
                @include('components.stat-card', [
                    'label' => 'Active Tasks',
                    'value' => $activeTasks->count(),
                    'icon' => 'icon-layers',
                    'color' => 'primary'
                ])
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                @include('components.stat-card', [
                    'label' => 'Pending Review',
                    'value' => 0,
                    'icon' => 'icon-eye',
                    'color' => 'warning'
                ])
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                @include('components.stat-card', [
                    'label' => 'Workload',
                    'value' => $project->members->count(),
                    'icon' => 'icon-people',
                    'color' => 'info'
                ])
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                @include('components.stat-card', [
                    'label' => 'Finished',
                    'value' => $completedTasks->count(),
                    'icon' => 'icon-check',
                    'color' => 'success'
                ])
            </div>
        </div>

        <div class="row g-4">
            {{-- Main Column --}}
            <div class="col-lg-8">
                <div class="d-flex flex-column gap-5">
                    {{-- Active List --}}
                    <section class="viso-fade-in">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="icon-list text-primary" style="font-size:18px"></i>
                                Backlog & Board
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill fs-10 fw-bold">{{ $activeTasks->count() }}</span>
                            </h5>
                        </div>

                        <div class="card border-0 shadow-sm p-3">
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border border-dashed mb-3 cursor-pointer hover-shadow transition-all"
                                 onclick="document.getElementById('projectInlineTask').focus()"
                                 style="border-color: rgba(var(--viso-primary-rgb), 0.3) !important; background: rgba(var(--viso-primary-rgb), 0.02) !important;">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                                    <i class="icon-plus text-primary" style="font-size:20px"></i>
                                </div>
                                <input id="projectInlineTask" type="text" placeholder="Add a task to {{ $project->name }}... press Enter"
                                       class="form-control-plaintext border-0 shadow-none p-0 flex-grow-1 small fw-bold text-dark"
                                       style="font-size: 1rem;"
                                       onkeydown="if(event.key==='Enter'&&this.value.trim()){VisoApp.addTask(this.value, {{ $project->id }});this.value='';}">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-white text-muted border border-secondary border-opacity-25 py-2 px-3 rounded-pill shadow-sm small fw-bold" style="font-size: 10px;">⏎ Enter to save</span>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                @forelse($activeTasks as $task)
                                    <div class="viso-task-hover-effect">
                                        @include('components.task-row', ['task' => $task])
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="icon-ghost d-block mb-2" style="font-size:40px;opacity:0.2"></i>
                                        <p class="mb-0 fw-bold">No active tasks in this project.</p>
                                        <p class="small opacity-50">Type above and press enter to create one! 🚀</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    {{-- Completed Section --}}
                    @if($completedTasks->count())
                    <section>
                        <button class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-2 mb-3 small fw-bold text-uppercase tracking-wider"
                                data-bs-toggle="collapse" data-bs-target="#projectDoneSection">
                            <i class="icon-arrow-down" style="font-size:12px"></i>
                            Done & Archived ({{ $completedTasks->count() }})
                        </button>
                        <div class="collapse ps-3 border-start border-3 border-success border-opacity-25" id="projectDoneSection">
                            <div class="d-flex flex-column gap-2">
                                @foreach($completedTasks->take(10) as $task)
                                    <div class="opacity-75 grayscale">
                                        @include('components.task-row', ['task' => $task])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    @endif
                </div>
            </div>

            {{-- Info Column --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 0;">
                    {{-- Progress --}}
                    @php
                        $total = $activeTasks->count() + $completedTasks->count();
                        $pct = $total > 0 ? round(($completedTasks->count() / $total) * 100) : 0;
                    @endphp
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-4 text-uppercase tracking-widest fs-10">Project Progress</h6>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="progress flex-grow-1" style="height:10px; border-radius: 5px;">
                                    <div class="progress-bar bg-primary rounded-pill shadow-sm" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="fw-bold text-dark small">{{ $pct }}%</span>
                            </div>
                            <p class="small text-muted mb-0">
                                {{ $completedTasks->count() }} of {{ $total }} tasks completed
                            </p>
                        </div>
                    </div>

                    {{-- Team --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 p-4 pb-0">
                            <h6 class="fw-bold text-dark mb-0 text-uppercase tracking-widest fs-10">Project Team</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex flex-column gap-3">
                                @foreach($project->members as $member)
                                    <div class="d-flex align-items-center justify-content-between viso-member-row" data-user-id="{{ $member->id }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $member->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&size=32&background=3b82f6&color=fff' }}" 
                                                 alt="User" class="rounded-circle" width="32" height="32">
                                            <div>
                                                <div class="fw-bold text-dark fs-13">{{ $member->name }}</div>
                                                <div class="text-muted fs-11">Member</div>
                                            </div>
                                        </div>
                                        @if($member->id !== $project->user_id)
                                        <button class="btn btn-link p-1 text-muted hover-text-danger border-0 shadow-none transition-all" 
                                                onclick="VisoApp.removeProjectMember({{ $project->id }}, {{ $member->id }})" title="Remove Member">
                                            <i class="icon-trash" style="font-size: 14px;"></i>
                                        </button>
                                        @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary tiny py-1">Owner</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-light w-100 mt-4 rounded-pill fs-12 fw-bold text-muted border-0 bg-opacity-50" 
                                    data-bs-toggle="modal" data-bs-target="#addProjectMemberModal">
                                <i class="icon-plus me-1"></i> Manage Team
                            </button>
                        </div>
                    </div>

                    {{-- Activity Card placeholder --}}
                    <div class="card border-0 shadow-sm bg-gradient-dark text-white">
                        <div class="card-body p-4">
                            <div class="d-flex gap-3">
                                <i class="icon-info text-primary fs-3"></i>
                                <div>
                                    <p class="small fw-bold mb-1">Board View available</p>
                                    <p class="fs-12 opacity-75 mb-0">Switch to Kanban mode to manage tasks by status across all projects.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Add Project Member Modal --}}
    <div class="modal fade" id="addProjectMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light bg-opacity-50 border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                            <i class="icon-user-follow text-primary" style="font-size:20px"></i>
                        </div>
                        Add Members to Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <p class="text-muted small mb-3">Select team members to add to <strong>{{ $project->name }}</strong>.</p>
                    
                    {{-- Search --}}
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-sm border-0 bg-light px-3 py-2" 
                               placeholder="Search members..." onkeyup="VisoApp.filterAddMembers(this.value)">
                    </div>

                    {{-- Chips Container --}}
                    <div class="d-flex flex-wrap gap-2 p-2 bg-light rounded-3" id="addProjectMemberSelection" 
                         style="max-height: 250px; overflow-y: auto; min-height: 100px;">
                        @forelse($availableUsers as $u)
                            <div class="viso-assignee-chip add-member-chip cursor-pointer transition-all border" 
                                 data-user-id="{{ $u->id }}"
                                 onclick="VisoApp.toggleAddMemberChip({{ $u->id }})"
                                 style="border-color: transparent;">
                                <img src="{{ $u->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&size=22&background=3b82f6&color=fff' }}" 
                                     class="rounded-circle" width="22" height="22">
                                <span class="small fw-medium">{{ $u->name }}</span>
                                <span class="remove-btn d-none">
                                    <i class="icon-x" style="font-size:12px"></i>
                                </span>
                            </div>
                        @empty
                            <div class="w-100 py-4 text-center text-muted small">
                                All available users are already members of this project.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="button" class="btn btn-primary fw-bold px-4" style="border-radius: 8px;"
                            onclick="VisoApp.addProjectMembers({{ $project->id }})">
                        Add Selected
                    </button>
                </div>
            </div>
        </div>
    </div>

{{-- Add Task to Project Modal - Reusing Quick Add Design --}}
<div class="modal fade" id="addProjectTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light bg-opacity-50 border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                        <i class="icon-zap text-primary" style="font-size:20px"></i>
                    </div>
                    Add Task to {{ $project->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Task Title</label>
                    <input type="text" id="projectTaskTitle" class="form-control form-control-lg border-0 bg-light shadow-none px-3" 
                           placeholder="What needs to be done in this project?" autofocus 
                           style="border-radius: 10px; font-size: 1.1rem; font-weight: 500;">
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                            <i class="icon-flag me-1" style="font-size:12px"></i> Priority
                        </label>
                        <select id="projectTaskPriority" class="form-select border-0 bg-light shadow-none" style="border-radius: 8px;">
                            <option value="Normal" selected>Normal</option>
                            <option value="Low">Low</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                            <i class="icon-calendar me-1" style="font-size:12px"></i> Due Date
                        </label>
                        <input type="date" id="projectTaskDue" class="form-control border-0 bg-light shadow-none" style="border-radius: 8px;">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted text-uppercase tracking-wider d-flex justify-content-between">
                        <span><i class="icon-users me-1" style="font-size:12px"></i> Assign To</span>
                        <span class="text-lowercase fw-normal opacity-50" style="font-size:10px">Select members</span>
                    </label>
                    
                    {{-- Hidden Select --}}
                    <select id="projectTaskAssignee" class="d-none" multiple>
                        @foreach($project->members as $u)
                            <option value="{{ $u->id }}" selected>{{ $u->name }}</option>
                        @endforeach
                    </select>

                    {{-- Chips Container --}}
                    <div class="d-flex flex-wrap gap-2 p-2 bg-light rounded-3" id="projectTaskAssigneeList" 
                         style="max-height: 150px; overflow-y: auto; min-height: 42px;">
                        @foreach($project->members as $u)
                            @php $isMe = $u->id === auth()->id(); @endphp
                            <div class="viso-assignee-chip project-task-chip cursor-pointer transition-all selected border-primary bg-primary bg-opacity-10" 
                                 data-user-id="{{ $u->id }}"
                                 onclick="VisoApp.toggleQuickTaskMember({{ $u->id }}, '#projectTaskAssignee', '.project-task-chip')"
                                 style="border: 1px solid transparent;">
                                <img src="{{ $u->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&size=22&background=3b82f6&color=fff' }}" 
                                     class="rounded-circle" width="22" height="22">
                                <span class="small fw-medium">{{ $u->name }}</span>
                                <span class="remove-btn ms-1">
                                    <i class="icon-x" style="font-size:12px; vertical-align: middle;"></i>
                                </span>
                            </div>
                        @endforeach
                        @if($project->members->isEmpty())
                            <div class="text-muted small italic p-1">No members in this project.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                <button type="button" class="btn btn-primary fw-bold px-4" onclick="VisoApp.addProjectTask({{ $project->id }})" style="border-radius: 8px;">
                    <i class="icon-plus me-1" style="font-size:16px"></i> Create Task
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Project Modal --}}
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light bg-opacity-50 border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3">
                        <i class="icon-pencil text-warning" style="font-size:20px"></i>
                    </div>
                    Edit Project
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 pb-4">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Project Name</label>
                        <input type="text" name="name" class="form-control form-control-lg border-0 bg-light shadow-none px-3" 
                               value="{{ $project->name }}" required
                               style="border-radius: 10px; font-size: 1.1rem; font-weight: 500;">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Description</label>
                        <textarea name="description" class="form-control border-0 bg-light shadow-none px-3" rows="3"
                                  placeholder="What is this project about?"
                                  style="border-radius: 10px;">{{ $project->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4" style="border-radius: 8px;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => VisoApp.toast('{{ session('success') }}'));</script>
@endif
@endsection

