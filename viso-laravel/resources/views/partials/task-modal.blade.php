{{-- Task Slide-Over Modal --}}
<div class="viso-backdrop" id="taskBackdrop"></div>
<div class="viso-slide-over" id="taskSlideOver">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom bg-white">
        <div class="d-flex align-items-center gap-2 text-muted small min-w-0">
            <i class="icon-folder text-primary" style="font-size:14px"></i>
            <span id="taskModalProject" class="fw-medium">Project</span>
            <i class="icon-chevron-right" style="font-size:12px"></i>
            <span id="taskModalTitle" class="text-dark text-truncate fw-medium" style="max-width:200px">Task Title</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light btn-sm border" data-bs-toggle="dropdown" title="More actions">
                    <i class="icon-more-horizontal" style="font-size:16px"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item small" href="#" onclick="event.preventDefault(); VisoApp.duplicateTask(VisoApp.getActiveTaskId())">
                        <i class="icon-copy me-2" style="font-size:14px"></i> Duplicate
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small text-danger" href="#" onclick="event.preventDefault(); VisoApp.deleteTask(VisoApp.getActiveTaskId())">
                        <i class="icon-trash-2 me-2" style="font-size:14px"></i> Delete
                    </a></li>
                </ul>
            </div>
            <button onclick="VisoApp.closeTaskModal()" class="btn btn-light btn-sm rounded-circle" style="width:32px;height:32px;padding:0">
                <i class="icon-x" style="font-size:18px"></i>
            </button>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row flex-grow-1 overflow-hidden">
        {{-- Left Column: Details --}}
        <div class="flex-grow-1 overflow-auto p-4 viso-scroll" id="taskDetailBody">
            {{-- Title --}}
            <h2 class="h4 fw-bold text-dark mb-4" id="taskDetailTitle" contenteditable="true"
                onblur="VisoApp.updateTaskField('title', this.textContent)"></h2>

            {{-- Quick Metadata Row --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-1">
                        <i class="icon-activity" style="font-size:12px"></i> Status
                    </label>
                    <select id="taskStatusSelect" class="form-select form-select-sm"
                            onchange="VisoApp.updateTaskField('status', this.value)">
                        <option>Todo</option>
                        <option>Scheduled</option>
                        <option>In Progress</option>
                        <option>Under Review</option>
                        <option>Completed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-1">
                        <i class="icon-flag" style="font-size:12px"></i> Priority
                    </label>
                    <select id="taskPrioritySelect" class="form-select form-select-sm"
                            onchange="VisoApp.updateTaskField('priority', this.value)">
                        <option>Low</option>
                        <option>Normal</option>
                        <option>High</option>
                        <option>Urgent</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-1">
                        <i class="icon-repeat" style="font-size:12px"></i> Recurrence
                    </label>
                    <select id="taskRecurrenceSelect" class="form-select form-select-sm"
                            onchange="VisoApp.updateTaskField('recurrence', this.value)">
                        <option value="none">None</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
            </div>

            {{-- Due Date & Estimate --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-1">
                        <i class="icon-calendar" style="font-size:12px"></i> Due Date
                    </label>
                    <input type="date" id="taskDueDateInput" class="form-control form-control-sm"
                           onchange="VisoApp.updateTaskField('due_date', this.value)">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-1">
                        <i class="icon-clock" style="font-size:12px"></i> Time Estimate (min)
                    </label>
                    <input type="number" id="taskTimeEstimate" class="form-control form-control-sm" min="0" step="15"
                           onchange="VisoApp.updateTaskField('time_estimate', this.value)">
                </div>
            </div>

            {{-- Assignees --}}
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-2">
                    <i class="icon-users" style="font-size:12px"></i> Assignees
                </label>
                <div class="d-flex flex-wrap gap-2 mb-2" id="taskAssigneeList"></div>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm border d-flex align-items-center gap-1 text-muted" data-bs-toggle="dropdown">
                        <i class="icon-user-plus" style="font-size:14px"></i> Add Assignee
                    </button>
                    <div class="dropdown-menu p-2 shadow-sm border-0" style="min-width:220px" id="assigneeDropdown">
                        {{-- Populated by JS --}}
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-1">
                    <i class="icon-file-text" style="font-size:12px"></i> Description
                </label>
                <div class="p-3 bg-light rounded-2 border" contenteditable="true" id="taskDescription"
                     style="min-height:60px"
                     onblur="VisoApp.updateTaskField('description', this.innerHTML)">
                    <p class="text-muted small mb-0">Click to add description...</p>
                </div>
            </div>

            {{-- Subtasks --}}
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h3 class="h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                        <i class="icon-check-square text-primary" style="font-size:16px"></i> Subtasks
                    </h3>
                    <span class="small text-muted" id="subtaskCount">0/0</span>
                </div>
                <div class="progress mb-3" style="height:4px">
                    <div class="progress-bar bg-success" id="subtaskProgress" style="width:0%"></div>
                </div>
                <div class="d-flex flex-column gap-1" id="subtaskList"></div>

                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded mt-2">
                    <i class="icon-plus text-muted" style="font-size:14px"></i>
                    <input type="text" id="newSubtaskInput" placeholder="Add subtask and press Enter..."
                           class="form-control form-control-sm border-0 bg-transparent shadow-none"
                           onkeydown="if(event.key==='Enter'){VisoApp.addSubtask(this.value);this.value='';}">
                </div>
            </div>
        </div>

        {{-- Right Column: Chat / Comments --}}
        <div class="bg-white d-flex flex-column border-start" style="width:320px;min-width:320px">
            <div class="p-3 border-bottom">
                <h3 class="h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                    <i class="icon-message-square text-primary" style="font-size:16px"></i> Comments
                </h3>
            </div>
            <div class="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-3 viso-scroll" id="chatMessages"></div>
            <div class="p-3 bg-white border-top">
                <form id="chatForm" onsubmit="event.preventDefault(); VisoApp.sendChatMessage();" class="position-relative">
                    <input type="text" id="chatInput" placeholder="Write a comment..."
                           class="form-control form-control-sm pe-5 bg-light border-0">
                    <button type="submit" class="btn btn-link btn-sm position-absolute top-50 end-0 translate-middle-y text-primary p-0 me-2">
                        <i class="icon-send" style="font-size:16px"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
