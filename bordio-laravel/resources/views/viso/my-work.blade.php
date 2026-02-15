@extends('layouts.app')
@section('title', 'My Work')

@section('content')
<div class="d-flex flex-column h-100 overflow-hidden bg-light bg-opacity-50">
    {{-- Main Header --}}
    <header class="flex-shrink-0 p-4 pb-0 viso-slide-up">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">
                    @php
                        $hour = now()->hour;
                        $greeting = match(true) {
                            $hour < 12 => 'Good morning',
                            $hour < 17 => 'Good afternoon',
                            default => 'Good evening'
                        };
                    @endphp
                    {{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}! 👋
                </h1>
                <p class="text-muted mb-0">
                    @php 
                        $handleCount = $overdueTasks->count() + $dueTodayTasks->count() + $upcomingTasks->count();
                    @endphp
                    @if($handleCount > 0)
                        You have <span class="fw-bold text-dark">{{ $handleCount }} {{ Str::plural('task', $handleCount) }} to handle today.</span>
                    @else
                        All caught up! Time to plan something new. ✨
                    @endif
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#quickAddTaskModal">
                    <i class="icon-plus" style="font-size:18px"></i>
                    <span class="fw-bold">New Task</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Scrollable Content Body --}}
    <div class="flex-grow-1 overflow-auto p-4 pt-2 viso-scroll">
        {{-- Metrics Row --}}
        <div class="row g-3 mb-5 viso-slide-shimmer">
            <div class="col-12 col-sm-6 col-md-3">
                @include('components.stat-card', [
                    'label' => 'Overdue',
                    'value' => $overdueTasks->count(),
                    'icon' => 'icon-alert-triangle',
                    'color' => 'danger',
                    'trend' => $overdueTasks->count() > 0 ? 'Urgent Need' : 'All Clear'
                ])
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                @include('components.stat-card', [
                    'label' => 'Due Today',
                    'value' => $dueTodayTasks->count(),
                    'icon' => 'icon-clock',
                    'color' => 'primary',
                    'trend' => 'Priority'
                ])
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                @include('components.stat-card', [
                    'label' => 'Upcoming',
                    'value' => $upcomingTasks->count(),
                    'icon' => 'icon-calendar',
                    'color' => 'warning',
                    'trend' => 'Next Steps'
                ])
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                @include('components.stat-card', [
                    'label' => 'Completed',
                    'value' => $completedTasks->count(),
                    'icon' => 'icon-check',
                    'color' => 'success',
                    'trend' => 'Good Progress'
                ])
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="row g-4">
            {{-- Left column for tasks --}}
            <div class="col-lg-8">
                <div class="d-flex flex-column gap-5">
                    
                    {{-- Overdue Tasks --}}
                    @if($overdueTasks->count())
                    <section class="viso-fade-in">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-danger mb-0 d-flex align-items-center gap-2">
                                <i class="icon-fire" style="font-size:18px"></i>
                                Critical Tasks
                                <span class="badge bg-danger rounded-pill fs-10 fw-bold">{{ $overdueTasks->count() }}</span>
                            </h5>
                        </div>
                        <div class="d-flex flex-column gap-2 card border-0 shadow-sm p-3">
                            @foreach($overdueTasks as $task)
                                <div class="viso-task-hover-effect">
                                    @include('components.task-row', ['task' => $task])
                                </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    {{-- Today's Focus --}}
                    <section class="viso-fade-in">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="icon-energy text-warning" style="font-size:18px"></i>
                                Today's Focus
                                <span class="badge bg-dark rounded-pill fs-10 fw-bold">{{ $dueTodayTasks->count() }}</span>
                            </h5>
                        </div>
                        <div class="card border-0 shadow-sm p-3">
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border border-dashed mb-3 cursor-pointer hover-shadow transition-all"
                                 onclick="document.getElementById('inlineNewTask').focus()">
                                <i class="icon-plus text-primary" style="font-size:20px"></i>
                                <input id="inlineNewTask" type="text" placeholder="Quick add a task for today... press Enter"
                                       class="form-control-plaintext border-0 shadow-none p-0 flex-grow-1 small fw-medium"
                                       onkeydown="if(event.key==='Enter'&&this.value.trim()){VisoApp.addTask(this.value);this.value='';}">
                                <span class="badge bg-white text-muted border border-secondary border-opacity-25 small">⏎ Enter</span>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                @forelse($dueTodayTasks as $task)
                                    <div class="viso-task-hover-effect">
                                        @include('components.task-row', ['task' => $task])
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="icon-trophy d-block mb-2" style="font-size:40px;opacity:0.2"></i>
                                        <p class="mb-0 fw-medium">All tasks for today are completed!</p>
                                        <p class="small opacity-50">You're ahead of schedule. 🎉</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    {{-- Next Actions --}}
                    <section class="viso-fade-in">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="icon-control-play text-primary" style="font-size:18px"></i>
                                Next Actions
                                <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill fs-10 fw-bold">{{ $upcomingTasks->count() }}</span>
                            </h5>
                        </div>
                        <div class="d-flex flex-column gap-2 card border-0 shadow-sm p-3">
                            @foreach($upcomingTasks as $task)
                                <div class="viso-task-hover-effect">
                                    @include('components.task-row', ['task' => $task])
                                </div>
                            @endforeach
                            @if($upcomingTasks->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <p class="small mb-0 opacity-50 italic">No upcoming tasks scheduled yet.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Completed Section --}}
                    @if($completedTasks->count())
                    <section>
                        <button class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-2 mb-3 small fw-bold text-uppercase tracking-wider"
                                data-bs-toggle="collapse" data-bs-target="#completedSection">
                            <i class="icon-arrow-down" style="font-size:12px"></i>
                            Recently Completed ({{ $completedTasks->count() }})
                        </button>
                        <div class="collapse ps-3 border-start border-3 border-success border-opacity-25" id="completedSection">
                            <div class="d-flex flex-column gap-2">
                                @foreach($completedTasks->take(10) as $task)
                                    <div class="opacity-75 grayscale">
                                        @include('components.task-row', ['task' => $task])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    @endif
                </div>
            </div>

            {{-- Right sidebar / Activity --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 0;">
                    {{-- Progress Ring --}}
                    @php
                        $total = $activeTasks->count() + $completedTasks->count();
                        $pct = $total > 0 ? round(($completedTasks->count() / $total) * 100) : 0;
                    @endphp
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                        <div class="card-body p-4 text-center">
                            <h6 class="fw-bold text-dark mb-4 text-uppercase tracking-widest fs-10">Daily Progress</h6>
                            <div class="viso-progress-ring mx-auto mb-4" style="--pct: {{ $pct }}; width: 120px; height: 120px;">
                                <svg width="120" height="120" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="54" fill="none" stroke="var(--viso-border-light)" stroke-width="8"/>
                                    <circle cx="60" cy="60" r="54" fill="none" stroke="var(--viso-primary)" stroke-width="8"
                                            stroke-dasharray="{{ 2 * 3.14159 * 54 }}"
                                            stroke-dashoffset="{{ 2 * 3.14159 * 54 * (1 - $pct / 100) }}"
                                            stroke-linecap="round" transform="rotate(-90 60 60)"/>
                                </svg>
                                <span class="viso-progress-ring-text fs-4 fw-bold text-dark">{{ $pct }}%</span>
                            </div>
                            <p class="mb-0 fw-bold text-dark fs-14">{{ $completedTasks->count() }} of {{ $total }}</p>
                            <p class="small text-muted mb-0">Tasks completed today</p>
                        </div>
                    </div>

                    {{-- Activity Card placeholder --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4 pb-0">
                            <h6 class="fw-bold text-dark mb-0 text-uppercase tracking-widest fs-10">Productivity Tip</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <i class="icon-bulb text-warning fs-3"></i>
                                </div>
                                <div>
                                    <p class="small text-dark fw-medium mb-1">Focus on one task at a time.</p>
                                    <p class="fs-12 text-muted mb-0">Multitasking can reduce productivity by up to 40%. Try using the Pomodoro technique to stay focused.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.quick-add-task-modal')
@endsection

