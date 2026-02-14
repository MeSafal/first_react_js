@extends('layouts.app')
@section('title', 'Kanban Board')

@section('content')
<div class="h-100 overflow-auto pb-3 viso-scroll px-4 pt-3">
    <div class="d-flex gap-4 h-100" style="width:max-content">
        @php
            $columns = ['Todo', 'Scheduled', 'In Progress', 'Under Review', 'Completed'];
        @endphp

        @foreach($columns as $status)
            @php $colTasks = $tasks->where('status', $status); @endphp
            <div class="viso-kanban-col">
                {{-- Column Header --}}
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold text-secondary mb-0">{{ $status }}</h6>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ $colTasks->count() }}</span>
                    </div>
                    <button class="btn btn-sm btn-link text-muted p-0"
                            onclick="VisoApp.promptAddTask('{{ $status }}')">
                        <i class="icon-plus" style="font-size:16px"></i>
                    </button>
                </div>

                {{-- Drop Zone --}}
                <div class="viso-kanban-drop"
                     data-kanban-status="{{ $status }}"
                     ondragover="event.preventDefault(); this.classList.add('drag-over')"
                     ondragleave="this.classList.remove('drag-over')"
                     ondrop="VisoApp.onKanbanDrop(event, '{{ $status }}')">
                    @foreach($colTasks as $task)
                        @include('components.kanban-card', ['task' => $task])
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
