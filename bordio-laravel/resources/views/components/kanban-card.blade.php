{{-- Reusable Kanban Card Component --}}
{{-- Usage: @include('components.kanban-card', ['task' => $task]) --}}

@php
    $assignees = $task->assignees ?? collect();
    $priorityClass = match($task->priority) {
        'Urgent' => 'bg-danger bg-opacity-10 text-danger',
        'High'   => 'bg-warning bg-opacity-10 text-warning',
        default  => 'bg-light text-secondary',
    };
@endphp

<div class="viso-kanban-card" draggable="true"
     data-task-id="{{ $task->id }}" data-status="{{ $task->status }}"
     ondragstart="VisoApp.onDragStart(event, {{ $task->id }})"
     onclick="VisoApp.openTaskModal({{ $task->id }})">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <span class="badge rounded-pill fw-normal {{ $priorityClass }}">{{ $task->priority }}</span>
        <div class="dropdown">
            <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown" onclick="event.stopPropagation()">
                <i class="icon-more-horizontal" style="font-size:14px"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item small" href="#" onclick="event.preventDefault(); VisoApp.duplicateTask({{ $task->id }})">
                    <i class="icon-copy me-2" style="font-size:14px"></i> Duplicate
                </a></li>
                <li><a class="dropdown-item small text-danger" href="#" onclick="event.preventDefault(); VisoApp.deleteTask({{ $task->id }})">
                    <i class="icon-trash-2 me-2" style="font-size:14px"></i> Delete
                </a></li>
            </ul>
        </div>
    </div>

    <h6 class="fw-bold text-dark mb-3 small">{{ $task->title }}</h6>

    <div class="d-flex align-items-center justify-content-between">
        <div class="viso-avatar-stack">
            @foreach($assignees->take(3) as $user)
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=24&background=3b82f6&color=fff' }}"
                     alt="{{ $user->name }}" class="rounded-circle border border-white" width="24" height="24"
                     title="{{ $user->name }}">
            @endforeach
        </div>
        <span class="small text-muted">{{ $task->time_estimate }}m</span>
    </div>
</div>
