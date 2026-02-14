@extends('layouts.app')
@section('title', 'Team Workload')

@section('content')
<div class="container-fluid py-4 h-100" style="max-width:1200px">
    <div class="card h-100 shadow-sm border-0 d-flex flex-column overflow-hidden">
        {{-- Header --}}
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark">Team Workload</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="small fw-medium text-muted">
                    {{ $weekStart->format('M j') }} – {{ $weekStart->copy()->addDays(6)->format('M j, Y') }}
                </span>
                <div class="btn-group">
                    <a href="{{ route('team-workload', ['week' => $weekStart->copy()->subWeek()->toDateString()]) }}"
                       class="btn btn-light btn-sm border"><i class="icon-chevron-left" style="font-size:16px"></i></a>
                    <a href="{{ route('team-workload', ['week' => $weekStart->copy()->addWeek()->toDateString()]) }}"
                       class="btn btn-light btn-sm border"><i class="icon-chevron-right" style="font-size:16px"></i></a>
                </div>
            </div>
        </div>

        {{-- Grid --}}
        <div class="flex-grow-1 overflow-auto viso-scroll">
            <div class="d-flex flex-column" style="min-width:900px">
                {{-- Date Header Row --}}
                <div class="d-flex border-bottom">
                    <div class="p-3 bg-light border-end text-center small fw-bold text-secondary text-uppercase"
                         style="width:200px;flex-shrink:0">Team Member</div>
                    @foreach($weekDays as $day)
                        <div class="flex-grow-1 p-3 text-center border-end small fw-bold {{ $day->isToday() ? 'bg-primary bg-opacity-10 text-primary' : 'bg-white text-muted' }}">
                            {{ $day->format('D j') }}
                        </div>
                    @endforeach
                </div>

                {{-- User Rows --}}
                @foreach($users as $user)
                    @php $userTasks = $tasks->filter(fn($t) => $t->assignees->contains('id', $user->id)); @endphp
                    <div class="d-flex border-bottom transition-all hover-bg-light">
                        {{-- User Info --}}
                        <div class="p-3 border-end d-flex align-items-center gap-3" style="width:200px;flex-shrink:0">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=32&background=3b82f6&color=fff' }}"
                                 alt="{{ $user->name }}" class="rounded-circle" width="32" height="32">
                            <div class="min-w-0">
                                <div class="small fw-bold text-dark text-truncate">{{ $user->name }}</div>
                                <div class="text-muted text-truncate fs-10">{{ $user->role ?? 'Member' }}</div>
                            </div>
                        </div>

                        {{-- Day Cells --}}
                        @foreach($weekDays as $day)
                            @php $dayTasks = $userTasks->filter(fn($t) => $t->due_date && $t->due_date->isSameDay($day)); @endphp
                            <div class="flex-grow-1 p-2 border-end" style="min-height:80px">
                                <div class="d-flex flex-column gap-1">
                                    @foreach($dayTasks as $task)
                                        <div class="p-1 rounded text-truncate border fs-10 cursor-pointer
                                            {{ $task->status === 'Completed' ? 'bg-success bg-opacity-10 text-success border-success-subtle' : 'bg-white text-dark border-light' }}"
                                             onclick="VisoApp.openTaskModal({{ $task->id }})">
                                            {{ $task->title }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
