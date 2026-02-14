{{-- Reusable Task Row Component --}}
{{-- Usage: @include('components.task-row', ['task' => $task]) --}}

@php
    $isCompleted = $task->status === 'Completed';
    $isOverdue = $task->due_date && $task->due_date->isPast() && !$isCompleted;
    $assignees = $task->assignees ?? collect();
@endphp

<div class="viso-task-row viso-fade-in" onclick="VisoApp.openTaskModal({{ $task->id }})" data-task-id="{{ $task->id }}">
    {{-- Status Check --}}
    <div class="d-flex align-items-center justify-content-center {{ $isCompleted ? 'text-success' : 'text-muted' }}">
        <i class="icon-{{ $isCompleted ? 'check-circle-2' : 'circle' }}" style="font-size:20px"></i>
    </div>

    {{-- Title --}}
    <div class="task-title {{ $isCompleted ? 'completed' : '' }}">
        {{ $task->title }}
    </div>

    {{-- Meta --}}
    <div class="d-flex align-items-center gap-3">
        {{-- Due Date --}}
        @if($task->due_date)
            <div class="d-flex align-items-center gap-1 small {{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                <i class="icon-clock" style="font-size:14px"></i>
                <span>{{ $task->due_date->format('M j') }}</span>
            </div>
        @endif

        {{-- Recurrence --}}
        @if($task->recurrence && $task->recurrence !== 'none')
            <div class="d-flex align-items-center gap-1 text-primary" title="Repeats {{ $task->recurrence }}">
                <i class="icon-repeat" style="font-size:14px"></i>
            </div>
        @endif

        {{-- Time Estimate --}}
        <span class="small text-muted">{{ $task->time_estimate }}m</span>

        {{-- Assignees --}}
        @if($assignees->count())
            <div class="viso-avatar-stack">
                @foreach($assignees->take(3) as $user)
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=24&background=3b82f6&color=fff' }}"
                         alt="{{ $user->name }}" class="rounded-circle border border-white" width="24" height="24"
                         title="{{ $user->name }}">
                @endforeach
                @if($assignees->count() > 3)
                    <span class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center text-muted fw-bold"
                          style="width:24px;height:24px;font-size:10px;margin-left:-8px">+{{ $assignees->count() - 3 }}</span>
                @endif
            </div>
        @endif
    </div>
</div>
