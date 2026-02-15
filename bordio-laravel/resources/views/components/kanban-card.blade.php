{{-- Kanban Card Component --}}
{{-- Usage: @include('components.kanban-card', ['task' => $task]) --}}

<div class="viso-kanban-card mb-2" 
     draggable="true" 
     data-task-id="{{ $task->id }}"
     ondragstart="VisoApp.onKanbanDragStart(event, {{ $task->id }})">
    
    @php
        $priorityColor = match($task->priority) {
            'Urgent' => 'danger',
            'High' => 'warning',
            'Low' => 'secondary',
            'Normal' => 'info',
            default => 'primary',
        };
        $isCompleted = $task->status === 'Completed';
    @endphp

    <div class="border p-2 rounded shadow-sm cursor-pointer transition-all hover-shadow viso-fade-in bg-{{ $priorityColor }} bg-opacity-10"
         onclick="VisoApp.openTaskModal({{ $task->id }})"
         style="border-left: 3px solid var(--viso-{{ $priorityColor }}) !important; opacity: {{ $isCompleted ? '0.6' : '1' }}">
        
        {{-- Title --}}
        <div class="fw-medium text-dark text-truncate small {{ $isCompleted ? 'text-decoration-line-through text-muted' : '' }}">
            {{ $task->title }}
        </div>

        {{-- Footer: Metadata + Assignee --}}
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div class="d-flex align-items-center gap-2 text-muted fs-10">
                @if($task->project)
                    <div class="viso-project-dot" style="background: var(--viso-primary)" title="{{ $task->project->name }}"></div>
                @endif
                
                @if($task->time_estimate > 0)
                    <span>{{ $task->time_estimate }}m</span>
                @endif

                @if($task->due_date)
                   @php $isOverdue = \Carbon\Carbon::parse($task->due_date)->isPast() && !$isCompleted; @endphp
                   <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                       {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                   </span>
                @endif
            </div>

            @if($task->assignees->count() > 0)
                <div class="d-flex align-items-center">
                    <img src="{{ $task->assignees->first()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($task->assignees->first()->name).'&size=16&background=3b82f6&color=fff' }}" 
                         class="rounded-circle" width="16" height="16" 
                         title="{{ $task->assignees->first()->name }}">
                    @if($task->assignees->count() > 1)
                        <span class="fs-10 text-muted ms-1">+{{ $task->assignees->count() - 1 }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
