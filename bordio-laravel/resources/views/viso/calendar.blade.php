@extends('layouts.app')
@section('title', 'Calendar')

@section('content')
<div class="d-flex h-100 align-items-stretch overflow-hidden">
    {{-- Main Calendar --}}
    <div class="flex-grow-1 d-flex flex-column h-100 bg-white">
        {{-- Toolbar --}}
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between viso-slide-up">
            <div class="d-flex align-items-center gap-3">
                <h1 class="h4 fw-bold text-dark mb-0 ls-1">
                    {{ \Carbon\Carbon::parse($weekStart)->format('F Y') }}
                </h1>
                <div class="btn-group shadow-sm">
                    <a href="{{ route('calendar', ['week' => \Carbon\Carbon::parse($weekStart)->subWeek()->toDateString()]) }}"
                       class="btn btn-light btn-sm border"><i class="icon-chevron-left" style="font-size:16px"></i></a>
                    <a href="{{ route('calendar') }}" class="btn btn-light btn-sm border fw-medium px-3">Today</a>
                    <a href="{{ route('calendar', ['week' => \Carbon\Carbon::parse($weekStart)->addWeek()->toDateString()]) }}"
                       class="btn btn-light btn-sm border"><i class="icon-chevron-right" style="font-size:16px"></i></a>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium">
                    Week View
                </span>
            </div>
        </div>

        {{-- Day Headers --}}
        <div class="d-flex border-bottom bg-light bg-opacity-50">
            @foreach($weekDays as $day)
                <div class="col py-3 small fw-bold text-muted text-uppercase text-center border-end fs-11 ls-1 {{ $day->isToday() ? 'text-primary bg-primary bg-opacity-5' : '' }}">
                    {{ $day->format('D') }}
                    <span class="d-block fs-4 text-dark mt-1 {{ $day->isToday() ? 'text-primary' : '' }}">{{ $day->format('j') }}</span>
                </div>
            @endforeach
        </div>

        {{-- Grid --}}
        <div class="flex-grow-1 overflow-auto viso-scroll">
            <div class="d-flex h-100" style="min-height:600px">
                @foreach($weekDays as $day)
                    @php
                        $dayTasks = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isSameDay($day));
                        $isToday = $day->isToday();
                        $totalMin = $dayTasks->sum('time_estimate');
                        $timeStr = $totalMin >= 60 ? floor($totalMin/60).'h '.($totalMin%60).'m' : $totalMin.'m';
                    @endphp
                    <div class="col border-end border-bottom p-2 transition-all {{ $isToday ? 'bg-primary bg-opacity-05' : 'bg-white' }}"
                         data-calendar-date="{{ $day->toDateString() }}"
                         ondragover="event.preventDefault(); this.classList.add('bg-primary','bg-opacity-10')"
                         ondragleave="this.classList.remove('bg-primary','bg-opacity-10')"
                         ondrop="VisoApp.onCalendarDrop(event, '{{ $day->toDateString() }}')">

                        {{-- Daily Summary --}}
                        @if($dayTasks->count())
                            <div class="mb-2 text-center">
                                <span class="badge bg-white border text-muted shadow-sm fs-10 fw-normal rounded-pill px-2">
                                    {{ $dayTasks->count() }} tasks • {{ $timeStr }}
                                </span>
                            </div>
                        @endif

                        <div class="d-flex flex-column gap-2">
                            @foreach($dayTasks as $task)
                                @php
                                    $priorityColor = match($task->priority) {
                                        'Urgent' => 'danger',
                                        'High' => 'warning',
                                        'Low' => 'secondary',
                                        default => 'primary',
                                    };
                                    $isCompleted = $task->status === 'Completed';
                                @endphp
                                <div class="bg-white border p-2 rounded shadow-sm cursor-pointer transition-all hover-shadow viso-fade-in"
                                     draggable="true" data-task-id="{{ $task->id }}"
                                     ondragstart="VisoApp.onDragStart(event, {{ $task->id }})"
                                     onclick="VisoApp.openTaskModal({{ $task->id }})"
                                     style="border-left: 3px solid var(--viso-{{ $priorityColor }}) !important; opacity: {{ $isCompleted ? '0.6' : '1' }}">
                                    <div class="fw-medium text-dark text-truncate small {{ $isCompleted ? 'text-decoration-line-through text-muted' : '' }}">
                                        {{ $task->title }}
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <div class="d-flex align-items-center gap-1 text-muted fs-10">
                                            @if($task->project)
                                                <div class="viso-project-dot" style="width:6px;height:6px;background:var(--viso-primary)"></div>
                                            @endif
                                            <span>{{ $task->time_estimate }}m</span>
                                        </div>
                                        @if($task->assignees->count())
                                            <img src="{{ $task->assignees->first()->avatar }}" class="rounded-circle" width="16" height="16">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Waiting List Sidebar --}}
    <div class="bg-light border-start d-flex flex-column" style="width:280px;min-width:280px">
        <div class="p-3 border-bottom bg-white">
            <h6 class="fw-bold text-dark small text-uppercase mb-1">Waiting List</h6>
            <p class="text-muted fs-11 mb-0">Drag tasks to calendar to schedule</p>
        </div>
        <div class="flex-grow-1 overflow-auto p-3 viso-scroll">
            @if($waitingList->count())
                <div class="d-flex flex-column gap-2">
                    @foreach($waitingList as $task)
                        <div class="bg-white border p-2 rounded shadow-sm cursor-grab hover-shadow transition-all viso-fade-in"
                             draggable="true" data-task-id="{{ $task->id }}"
                             ondragstart="VisoApp.onDragStart(event, {{ $task->id }})">
                            <div class="fw-medium text-dark small text-truncate">{{ $task->title }}</div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="text-muted fs-10">{{ $task->time_estimate }}m</span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary fs-10 border rounded-pill">Unscheduled</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="icon-check-circle d-block mb-2" style="font-size:32px;opacity:0.2"></i>
                    <p class="small fst-italic">All tasks scheduled!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
