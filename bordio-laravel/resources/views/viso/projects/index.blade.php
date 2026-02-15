@extends('layouts.app')
@section('title', 'All Projects')

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;">
    {{-- Header --}}
    <header class="mb-5 viso-slide-up">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">Projects</h1>
                <p class="text-muted mb-0">Overview of all active projects and initiatives</p>
            </div>
            <button class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 fw-bold" 
                    data-bs-toggle="modal" data-bs-target="#createProjectModal" style="border-radius: 10px;">
                <i class="icon-plus" style="font-size:18px"></i>
                New Project
            </button>
        </div>
    </header>

    {{-- Project Grid --}}
    <div class="row g-4">
        @forelse($projects as $project)
            @php
                $activeTasks = $project->tasks->where('status', '!=', 'Completed');
                $completedTasks = $project->tasks->where('status', '==', 'Completed');
                $totalTasks = $project->tasks->count();
                $progress = $totalTasks > 0 ? round(($completedTasks->count() / $totalTasks) * 100) : 0;
            @endphp
            <div class="col-md-6 col-lg-4 viso-fade-in" style="animation-delay: {{ $loop->index * 0.05 }}s">
                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none project-card-anchor">
                    <div class="card border-0 shadow-sm hover-shadow-lg transition-all h-100 project-card" style="border-radius: 16px; border: 1px solid rgba(0,0,0,0.05) !important;">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex flex-column">
                                    <h5 class="fw-bold text-dark mb-1">{{ $project->name }}</h5>
                                    <p class="text-muted small mb-0">{{ $project->team->name ?? 'Direct Project' }}</p>
                                </div>
                                <div class="dropdown" onclick="event.preventDefault(); event.stopPropagation();">
                                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                        <i class="icon-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li><a class="dropdown-item small" href="{{ route('projects.show', $project) }}">Open Details</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item small text-danger" href="#" 
                                               onclick="if(confirm('Delete project?')) document.getElementById('delete-project-{{ $project->id }}').submit();">Delete</a></li>
                                    </ul>
                                    <form id="delete-project-{{ $project->id }}" action="{{ route('projects.destroy', $project) }}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-muted">Tasks Completed</span>
                                    <span class="small fw-bold text-primary">{{ $progress }}%</span>
                                </div>
                                <div class="progress mb-4" style="height: 8px; border-radius: 4px; background: rgba(0,0,0,0.03);">
                                    <div class="progress-bar bg-primary" style="width: {{ $progress }}%; border-radius: 4px; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);"></div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: rgba(0,0,0,0.03) !important;">
                                    <div class="viso-avatar-stack">
                                        @foreach($project->members->take(4) as $member)
                                            <img src="{{ $member->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&size=24&background=3b82f6&color=fff' }}" 
                                                 class="rounded-circle border border-2 border-white" width="28" height="28" title="{{ $member->name }}">
                                        @endforeach
                                        @if($project->members->count() > 4)
                                            <div class="viso-avatar-placeholder border border-2 border-white">+{{ $project->members->count() - 4 }}</div>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-muted small fw-medium bg-light px-2 py-1 rounded-pill">
                                        <i class="icon-check-square" style="font-size:12px"></i> 
                                        <span>{{ $completedTasks->count() }}/{{ $totalTasks }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="bg-light p-5 rounded-4 d-inline-block">
                    <i class="icon-folder-plus text-muted mb-3" style="font-size: 48px; opacity: 0.3;"></i>
                    <h4 class="fw-bold text-dark">No projects yet</h4>
                    <p class="text-muted mb-4">Start by creating your first project to organize your tasks.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        Create Project
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
