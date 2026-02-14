@extends('layouts.app')
@section('title', 'Team Workload')

@section('content')
<div class="d-flex flex-column h-100 overflow-hidden">
    {{-- Header --}}
    <header class="flex-shrink-0 p-4 pb-0 viso-slide-up">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">Team Workload</h1>
                <p class="text-muted mb-0">View capacity and assignments across the team</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <h4 class="h6 fw-bold text-dark mb-0 ls-1">
                    {{ \Carbon\Carbon::parse($weekStart)->format('M j') }} - {{ \Carbon\Carbon::parse($weekStart)->addDays(6)->format('M j, Y') }}
                </h4>
                <div class="btn-group shadow-sm">
                    <a href="{{ route('team-workload', ['week' => \Carbon\Carbon::parse($weekStart)->subWeek()->toDateString()]) }}"
                       class="btn btn-light btn-sm border"><i class="icon-chevron-left" style="font-size:16px"></i></a>
                    <a href="{{ route('team-workload') }}" class="btn btn-light btn-sm border fw-medium px-3">Today</a>
                    <a href="{{ route('team-workload', ['week' => \Carbon\Carbon::parse($weekStart)->addWeek()->toDateString()]) }}"
                       class="btn btn-light btn-sm border"><i class="icon-chevron-right" style="font-size:16px"></i></a>
                </div>
            </div>
        </div>
    </header>

    {{-- Grid --}}
    <div class="flex-grow-1 overflow-auto px-4 pb-4 viso-scroll">
        <div class="bg-white rounded-3 border shadow-sm h-100 d-flex flex-column" style="min-width:1000px">
            {{-- Header Row --}}
            <div class="d-flex border-bottom bg-light bg-opacity-50">
                <div class="p-3 border-end fw-bold text-muted small text-uppercase" style="width:240px;min-width:240px">
                    Team Member
                </div>
                @foreach($weekDays as $day)
                    <div class="flex-grow-1 p-3 border-end text-center small fw-bold text-uppercase {{ $day->isToday() ? 'text-primary bg-primary bg-opacity-05' : 'text-muted' }}">
                        {{ $day->format('D j') }}
                    </div>
                @endforeach
            </div>

            {{-- User Rows --}}
            <div class="flex-grow-1 overflow-auto viso-scroll">
                @foreach($users as $user)
                    <div class="d-flex border-bottom hover-bg-light transition-all viso-fade-in" style="min-height:100px; animation-delay: {{ $loop->index * 0.05 }}s">
                        {{-- User Column --}}
                        <div class="p-3 border-end d-flex align-items-start gap-3 bg-white" style="width:240px;min-width:240px;position:sticky;left:0;z-index:10">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=40&background=3b82f6&color=fff' }}"
                                 class="rounded-circle shadow-sm" width="40" height="40">
                            <div>
                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                <div class="text-muted fs-11">{{ $user->role ?? 'Member' }}</div>
                                {{-- Simple stats --}}
                                <div class="mt-2 fs-10 text-muted">
                                    {{ $tasks->whereIn('id', $user->tasks->pluck('id'))->count() }} active tasks
                                </div>
                            </div>
                        </div>

                        {{-- Days Columns --}}
                        @foreach($weekDays as $day)
                            @php
                                $dayTasks = $tasks->filter(function($t) use ($user, $day) {
                                    return $t->assignees->contains('id', $user->id) &&
                                           $t->due_date &&
                                           $t->due_date->isSameDay($day);
                                });
                            @endphp
                            <div class="flex-grow-1 p-2 border-end d-flex flex-column gap-2" style="min-width:120px">
                                @foreach($dayTasks as $task)
                                    @php
                                        $priorityColor = match($task->priority) {
                                            'Urgent' => 'danger',
                                            'High' => 'warning',
                                            'Low' => 'secondary',
                                            default => 'primary',
                                        };
                                    @endphp
                                    <div class="bg-white border rounded p-2 shadow-sm cursor-pointer hover-shadow transition-all"
                                         style="border-left: 3px solid var(--viso-{{ $priorityColor }}) !important"
                                         onclick="VisoApp.openTaskModal({{ $task->id }})">
                                        <div class="fw-medium text-dark small text-truncate">{{ $task->title }}</div>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <span class="text-muted fs-10">{{ $task->time_estimate }}m</span>
                                            @if($task->status === 'Completed')
                                                <i class="icon-check-circle text-success" style="font-size:10px"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
