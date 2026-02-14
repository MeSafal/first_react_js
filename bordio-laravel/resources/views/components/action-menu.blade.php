{{-- Reusable Action Menu Component --}}
{{-- Usage: @include('components.action-menu', ['taskId' => $task->id]) --}}

<div class="dropdown d-inline-block">
    <button class="btn btn-sm btn-link text-muted p-1" data-bs-toggle="dropdown" onclick="event.stopPropagation()">
        <i class="icon-more-horizontal" style="font-size:16px"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
        <li>
            <a class="dropdown-item small d-flex align-items-center gap-2" href="#"
               onclick="event.preventDefault(); VisoApp.duplicateTask({{ $taskId }})">
                <i class="icon-copy" style="font-size:14px"></i> Duplicate
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item small d-flex align-items-center gap-2 text-danger" href="#"
               onclick="event.preventDefault(); VisoApp.deleteTask({{ $taskId }})">
                <i class="icon-trash-2" style="font-size:14px"></i> Delete
            </a>
        </li>
    </ul>
</div>
