@extends('layouts.app')
@section('title', 'Calendar')

@section('content')
<div class="container-fluid h-100 p-0 overflow-hidden">
    <div class="row g-0 h-100">
        {{-- Main Calendar (9/12) --}}
        <div class="col-lg-9 d-flex flex-column h-100 border-end bg-white">
            {{-- Toolbar - Stays fixed at top --}}
            <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between viso-slide-up bg-white" style="z-index: 30">
                <div class="d-flex align-items-center gap-4">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-0 tracking-tight">
                            {{ \Carbon\Carbon::parse($weekStart)->format('F') }} 
                            <span class="text-muted fw-normal">{{ \Carbon\Carbon::parse($weekStart)->format('Y') }}</span>
                        </h1>
                    </div>
                    <div class="d-flex align-items-center gap-1 bg-light p-1 rounded-3">
                        <a href="{{ route('calendar', ['week' => \Carbon\Carbon::parse($weekStart)->subWeek()->toDateString()]) }}"
                           class="btn btn-sm btn-white border-0 shadow-sm rounded-2 p-2 hover-bg-white transition-all text-dark">
                           <i class="icon-chevron-left" style="font-size:16px"></i>
                        </a>
                        <a href="{{ route('calendar') }}" class="btn btn-sm btn-white border-0 shadow-sm rounded-2 px-3 py-2 fw-bold text-dark hover-bg-white transition-all">Today</a>
                        <a href="{{ route('calendar', ['week' => \Carbon\Carbon::parse($weekStart)->addWeek()->toDateString()]) }}"
                           class="btn btn-sm btn-white border-0 shadow-sm rounded-2 p-2 hover-bg-white transition-all text-dark">
                           <i class="icon-chevron-right" style="font-size:16px"></i>
                        </a>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 bg-light px-3 py-2 rounded-pill shadow-sm">
                    <div class="viso-project-dot bg-primary" style="width:8px; height:8px;"></div>
                    <span class="small fw-bold text-dark">Weekly Schedule</span>
                </div>
            </div>

            {{-- Unified Scroll Container for Header & Grid --}}
            <div class="flex-grow-1 overflow-auto viso-scroll">
                <div class="d-flex flex-column" style="min-width: 1000px; min-height: 100%">
                    {{-- Day Headers --}}
                    <div class="d-flex border-bottom bg-white sticky-top shadow-sm" style="z-index: 20; top: 0;">
                        @foreach($weekDays as $day)
                            <div class="col py-3 small fw-bold text-muted text-uppercase text-center border-end fs-11 ls-1 position-relative bg-white" 
                                 style="border-color: rgba(0,0,0,0.05) !important">
                                {{ $day->format('D') }}
                                <div class="d-flex align-items-center justify-content-center mx-auto mt-1" 
                                     style="width: 32px; height: 32px; border-radius: 50%; {{ $day->isToday() ? 'background: var(--viso-primary); color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);' : 'color: var(--bs-dark);' }}">
                                    <span class="fs-5 fw-bold">{{ $day->format('j') }}</span>
                                </div>
                                @if($day->isToday())
                                    <div class="position-absolute bottom-0 start-0 w-100" style="height: 3px; background: var(--viso-primary)"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Task Grid --}}
                    <div class="d-flex flex-grow-1">
                        @foreach($weekDays as $day)
                            @php
                                $dayTasks = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isSameDay($day));
                                $totalMin = $dayTasks->sum('time_estimate');
                                $timeStr = $totalMin >= 60 ? floor($totalMin/60).'h '.($totalMin%60).'m' : $totalMin.'m';
                            @endphp
                            <div class="col border-end border-bottom p-2 transition-all bg-white {{ $day->isToday() ? 'viso-today-col' : '' }}"
                                 data-calendar-date="{{ $day->toDateString() }}"
                                 ondragover="event.preventDefault(); this.classList.add('bg-primary','bg-opacity-05')"
                                 ondragleave="this.classList.remove('bg-primary','bg-opacity-05')"
                                 ondrop="VisoApp.onCalendarDrop(event, '{{ $day->toDateString() }}')">

                                @if($dayTasks->count())
                                    <div class="mb-2 text-center">
                                        <span class="badge bg-light border text-muted shadow-none fs-10 fw-normal rounded-pill px-2">
                                            {{ $dayTasks->count() }} tasks • {{ $timeStr }}
                                        </span>
                                    </div>
                                @endif

                                <div class="d-flex flex-column gap-2">
                                    @foreach($dayTasks as $task)
                                        @include('components.kanban-card', ['task' => $task])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Waiting List Sidebar (3/12) --}}
        <div class="col-lg-3 d-flex flex-column h-100 bg-white border-start">
            <div class="p-4 border-bottom">
                <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-3 fw-bold shadow-sm mb-4" 
                        data-bs-toggle="modal" data-bs-target="#quickAddTaskModal" style="border-radius: 12px;">
                    <i class="icon-plus-circle" style="font-size:20px"></i>
                    Quick Add Task
                </button>

                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Backlog</h6>
                        <p class="text-muted fs-11 mb-0">Drag to calendar to schedule</p>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                        <i class="icon-drawer text-primary" style="font-size:18px"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 overflow-auto p-4 viso-scroll bg-light bg-opacity-50">
                @if($waitingList->count())
                    <div class="d-flex flex-column gap-3">
                        @foreach($waitingList as $task)
                            <div class="viso-backlog-card bg-white border p-3 rounded-3 cursor-grab hover-shadow transition-all viso-fade-in shadow-sm border-0"
                                 draggable="true" data-task-id="{{ $task->id }}"
                                 ondragstart="VisoApp.onDragStart(event, {{ $task->id }})"
                                 style="border-left: 4px solid var(--viso-primary) !important;">
                                <div class="fw-bold text-dark small mb-2 lh-base">{{ $task->title }}</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-1 text-muted fs-11">
                                        <i class="icon-clock" style="font-size:10px"></i>
                                        <span>{{ $task->time_estimate }}m</span>
                                    </div>
                                    @if($task->project)
                                        <span class="badge bg-light text-muted fw-normal fs-10 border rounded-pill">{{ \Illuminate\Support\Str::limit($task->project->name, 15) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 px-4 opacity-75">
                        <div class="bg-white p-4 rounded-circle d-inline-flex mb-3 shadow-sm border border-light">
                            <i class="icon-check-circle text-success" style="font-size:32px"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">All Caught Up!</h6>
                        <p class="small text-muted">No pending tasks in your backlog.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@include('partials.quick-add-task-modal')
