{{-- Task Row Component --}}
@php
    $priorityColor = match($task->priority) {
        'Urgent' => 'danger',
        'High' => 'warning',
        'Low' => 'secondary',
        default => 'primary',
    };
    $isOverdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'Completed';
    $isCompleted = $task->status === 'Completed';
    $assignees = $task->assignees ?? collect();
@endphp

<div class="viso-task-row viso-fade-in {{ $isCompleted ? 'opacity-75' : '' }}"
     onclick="VisoApp.openTaskModal({{ $task->id }})"
     style="border-left: 3px solid var(--viso-{{ $priorityColor }}) !important">
    
    {{-- Checkbox --}}
    <div onclick="event.stopPropagation()" class="p-2 me-1 cursor-pointer hover-bg-light rounded-circle transition-all d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
        <div class="form-check m-0 p-0">
            <input class="form-check-input ms-0 cursor-pointer" type="checkbox"
                   style="width: 1.15rem; height: 1.15rem;"
                   {{ $isCompleted ? 'checked' : '' }}
                   onchange="VisoApp.updateTaskField({{ $task->id }}, 'status', this.checked ? 'Completed' : 'Todo')">
        </div>
    </div>

    {{-- Title & Tags --}}
    <div class="d-flex flex-column flex-grow-1 ms-2" style="min-width:0">
        <div class="d-flex align-items-center gap-2">
            <span class="task-title text-truncate {{ $isCompleted ? 'completed' : 'text-dark' }}">
                {{ $task->title }}
            </span>
            @if($task->project)
                <span class="badge bg-light text-muted border fs-10 fw-normal d-none d-md-inline-block">
                    {{ $task->project->name }}
                </span>
            @endif
        </div>
        
        {{-- Metadata Row --}}
        <div class="d-flex align-items-center gap-3 mt-1 text-muted fs-10">
            @if($task->due_date)
                <span class="d-flex align-items-center gap-1 {{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                    <i class="icon-calendar" style="font-size:10px"></i>
                    {{ $task->due_date->format('M j') }}
                </span>
            @endif
            
            <span class="d-flex align-items-center gap-1">
                <i class="icon-clock" style="font-size:10px"></i>
                {{ $task->time_estimate }}m
            </span>

            @if($task->active_subtasks_count > 0)
                <span class="d-flex align-items-center gap-1">
                    <i class="icon-check-square" style="font-size:10px"></i>
                    {{ $task->completed_subtasks_count }}/{{ $task->subtasks_count }}
                </span>
            @endif
        </div>
    </div>

    {{-- Assignees --}}
    @if($assignees->count())
        <div class="viso-avatar-stack d-none d-sm-flex ms-3">
            @foreach($assignees->take(3) as $user)
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=24&background=3b82f6&color=fff' }}"
                     alt="{{ $user->name }}" class="rounded-2 border border-white" width="24" height="24"
                     title="{{ $user->name }}">
            @endforeach
            @if($assignees->count() > 3)
                <span class="rounded-2 bg-light border d-inline-flex align-items-center justify-content-center text-muted fw-bold"
                      style="width:24px;height:24px;font-size:10px;margin-left:-6px">+{{ $assignees->count() - 3 }}</span>
            @endif
        </div>
    @endif
</div>
