{{-- Kanban Card Component --}}
{{-- Usage: @include('components.kanban-card', ['task' => $task]) --}}

<div class="viso-kanban-card" 
     draggable="true" 
     data-task-id="{{ $task->id }}"
     ondragstart="VisoApp.onKanbanDragStart(event, {{ $task->id }})">
    
    {{-- Priority indicator --}}
    @php
        $priorityColors = [
            'Low' => 'secondary',
            'Normal' => 'info',
            'High' => 'warning',
            'Urgent' => 'danger'
        ];
        $priorityColor = $priorityColors[$task->priority] ?? 'secondary';
    @endphp
    <div class="viso-priority-bar bg-{{ $priorityColor }}"></div>

    {{-- Card Content --}}
    <div class="p-3">
        {{-- Title --}}
        <h6 class="fw-medium text-dark mb-2 cursor-pointer hover-text-primary transition-all" 
            onclick="VisoApp.openTaskModal({{ $task->id }})">
            {{ $task->title }}
        </h6>

        {{-- Metadata Row --}}
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            {{-- Project Badge --}}
            @if($task->project)
                <span class="badge bg-light text-dark border small">
                    <i class="icon-folder" style="font-size:12px"></i>
                    {{ $task->project->name }}
                </span>
            @endif

            {{-- Priority Badge --}}
            <span class="badge bg-{{ $priorityColor }} bg-opacity-10 text-{{ $priorityColor }} small">
                {{ $task->priority }}
            </span>

            {{-- Due Date --}}
            @if($task->due_date)
                @php
                    $dueDate = \Carbon\Carbon::parse($task->due_date);
                    $isOverdue = $dueDate->isPast() && $task->status !== 'Completed';
                    $isToday = $dueDate->isToday();
                @endphp
                <span class="badge small {{ $isOverdue ? 'bg-danger text-white' : ($isToday ? 'bg-warning text-dark' : 'bg-light text-muted border') }}">
                    <i class="icon-calendar" style="font-size:11px"></i>
                    {{ $dueDate->format('M d') }}
                </span>
            @endif

            {{-- Time Estimate --}}
            @if($task->time_estimate > 0)
                <span class="badge bg-light text-muted border small">
                    <i class="icon-clock" style="font-size:11px"></i>
                    {{ floor($task->time_estimate / 60) }}h {{ $task->time_estimate % 60 }}m
                </span>
            @endif
        </div>

        {{-- Subtasks Progress --}}
        @if($task->subtasks->count() > 0)
            @php
                $completedSubtasks = $task->subtasks->where('completed', true)->count();
                $totalSubtasks = $task->subtasks->count();
                $progressPct = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0;
            @endphp
            <div class="mb-2">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="small text-muted">
                        <i class="icon-check-square" style="font-size:12px"></i>
                        {{ $completedSubtasks }}/{{ $totalSubtasks }} subtasks
                    </span>
                    <span class="small fw-medium text-{{ $progressPct === 100 ? 'success' : 'primary' }}">
                        {{ $progressPct }}%
                    </span>
                </div>
                <div class="progress" style="height:4px">
                    <div class="progress-bar bg-{{ $progressPct === 100 ? 'success' : 'primary' }}" 
                         role="progressbar" 
                         style="width: {{ $progressPct }}%" 
                         aria-valuenow="{{ $progressPct }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
        @endif

        {{-- Tags --}}
        @if($task->tags && is_array($task->tags))
            <div class="d-flex gap-1 flex-wrap mb-2">
                @foreach($task->tags as $tag)
                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size:10px">
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Footer: Assignees + Actions --}}
        <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
            {{-- Assignee Avatars --}}
            <div class="d-flex align-items-center">
                @if($task->assignees->count() > 0)
                    <div class="viso-avatar-stack">
                        @foreach($task->assignees->take(3) as $assignee)
                            <div class="viso-avatar viso-avatar-sm" 
                                 title="{{ $assignee->name }}"
                                 style="background: linear-gradient(135deg, {{ '#' . substr(md5($assignee->name), 0, 6) }}, {{ '#' . substr(md5($assignee->name), 6, 6) }})">
                                {{ strtoupper(substr($assignee->name, 0, 1)) }}
                            </div>
                        @endforeach
                        @if($task->assignees->count() > 3)
                            <div class="viso-avatar viso-avatar-sm bg-light text-muted border">
                                +{{ $task->assignees->count() - 3 }}
                            </div>
                        @endif
                    </div>
                @else
                    <span class="small text-muted fst-italic">Unassigned</span>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div class="d-flex gap-1">
                {{-- Chat indicator --}}
                @if($task->chatMessages->count() > 0)
                    <button class="btn btn-sm btn-link text-muted p-1 hover-text-primary" 
                            onclick="event.stopPropagation(); VisoApp.openTaskModal({{ $task->id }}, 'chat')"
                            title="{{ $task->chatMessages->count() }} messages">
                        <i class="icon-bubble" style="font-size:14px"></i>
                        <small class="ms-1">{{ $task->chatMessages->count() }}</small>
                    </button>
                @endif
                
                {{-- Edit --}}
                <button class="btn btn-sm btn-link text-muted p-1 hover-text-primary" 
                        onclick="event.stopPropagation(); VisoApp.openTaskModal({{ $task->id }})"
                        title="Edit task">
                    <i class="icon-pencil" style="font-size:14px"></i>
                </button>
            </div>
        </div>
    </div>
</div>
