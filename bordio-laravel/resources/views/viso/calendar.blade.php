@extends('layouts.app')
@section('title', 'Calendar')

@section('content')
<div class="d-flex h-100 align-items-stretch">
    {{-- Main Calendar --}}
    <div class="flex-grow-1 d-flex flex-column h-100 bg-white">
        {{-- Toolbar --}}
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <h1 class="h5 fw-bold text-dark mb-0" id="calMonthTitle">
                    {{ \Carbon\Carbon::parse($weekStart)->format('F Y') }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('calendar', ['week' => \Carbon\Carbon::parse($weekStart)->subWeek()->toDateString()]) }}"
                       class="btn btn-light btn-sm"><i class="icon-chevron-left" style="font-size:16px"></i></a>
                    <a href="{{ route('calendar', ['week' => \Carbon\Carbon::parse($weekStart)->addWeek()->toDateString()]) }}"
                       class="btn btn-light btn-sm"><i class="icon-chevron-right" style="font-size:16px"></i></a>
                </div>
            </div>
            <div class="small text-muted">Week View</div>
        </div>

        {{-- Day Headers --}}
        <div class="d-flex border-bottom">
            @foreach($weekDays as $day)
                <div class="col py-2 small fw-bold text-muted text-uppercase text-center border-end fs-11">
                    {{ $day->format('D') }}
                </div>
            @endforeach
        </div>

        {{-- Grid --}}
        <div class="flex-grow-1 overflow-auto">
            <div class="d-flex h-100">
                @foreach($weekDays as $day)
                    @php
                        $dayTasks = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isSameDay($day));
                        $isToday = $day->isToday();
                        $totalMin = $dayTasks->sum('time_estimate');
                        $timeStr = $totalMin >= 60 ? floor($totalMin/60).'h '.($totalMin%60).'m' : $totalMin.'m';
                    @endphp
                    <div class="col border-end border-bottom p-2 {{ $isToday ? 'bg-light' : 'bg-white' }}"
                         style="min-height:200px"
                         data-calendar-date="{{ $day->toDateString() }}"
                         ondragover="event.preventDefault(); this.classList.add('bg-primary','bg-opacity-10')"
                         ondragleave="this.classList.remove('bg-primary','bg-opacity-10')"
                         ondrop="VisoApp.onCalendarDrop(event, '{{ $day->toDateString() }}')">

                        <div class="small fw-medium mb-2 d-flex justify-content-between align-items-center {{ $isToday ? 'text-primary' : 'text-muted' }}">
                            <span>{{ $day->format('D j') }}</span>
                            @if($dayTasks->count())
                                <span class="badge bg-light text-secondary border fs-10">{{ $timeStr }}</span>
                            @endif
                        </div>

                        <div class="d-flex flex-column gap-2">
                            @foreach($dayTasks as $task)
                                <div class="bg-white border p-2 rounded shadow-sm cursor-pointer transition-all"
                                     draggable="true" data-task-id="{{ $task->id }}"
                                     ondragstart="VisoApp.onDragStart(event, {{ $task->id }})"
                                     onclick="VisoApp.openTaskModal({{ $task->id }})">
                                    <div class="fw-medium text-dark text-truncate small">{{ $task->title }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <div class="rounded-circle {{ $task->status === 'Completed' ? 'bg-success' : 'bg-primary' }}"
                                             style="width:6px;height:6px"></div>
                                        <span class="text-muted fs-10">{{ $task->time_estimate }}m</span>
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
    <div class="bg-light border-start d-flex flex-column" style="width:260px;min-width:260px">
        <div class="p-3 border-bottom">
            <h6 class="fw-bold text-secondary small text-uppercase mb-0">Waiting List</h6>
        </div>
        <div class="flex-grow-1 overflow-auto p-2 viso-scroll">
            @foreach($waitingList as $task)
                <div class="bg-white border p-2 rounded shadow-sm mb-2 cursor-grab"
                     draggable="true" data-task-id="{{ $task->id }}"
                     ondragstart="VisoApp.onDragStart(event, {{ $task->id }})">
                    <div class="fw-medium text-dark small text-truncate">{{ $task->title }}</div>
                    <span class="text-muted fs-10">{{ $task->time_estimate }}m</span>
                </div>
            @endforeach
            @if($waitingList->isEmpty())
                <div class="text-muted small fst-italic p-2">No unscheduled tasks</div>
            @endif
        </div>
    </div>
</div>
@endsection
