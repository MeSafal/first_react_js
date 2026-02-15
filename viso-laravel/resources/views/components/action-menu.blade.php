{{-- Task Action Menu --}}
<div class="dropdown" onclick="event.stopPropagation()">
    <button class="btn btn-link btn-sm text-muted p-1" data-bs-toggle="dropdown" style="opacity:0.5">
        <i class="icon-more-vertical" style="font-size:16px"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
        <li><a class="dropdown-item small" href="#" onclick="event.preventDefault(); VisoApp.openTaskModal({{ $taskId }})">
            <i class="icon-eye me-2" style="font-size:14px"></i> View Details
        </a></li>
        <li><a class="dropdown-item small" href="#" onclick="event.preventDefault(); VisoApp.duplicateTask({{ $taskId }})">
            <i class="icon-copy me-2" style="font-size:14px"></i> Duplicate
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item small text-danger" href="#" onclick="event.preventDefault(); VisoApp.deleteTask({{ $taskId }})">
            <i class="icon-trash-2 me-2" style="font-size:14px"></i> Delete
        </a></li>
    </ul>
</div>
