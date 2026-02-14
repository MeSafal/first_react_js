{{-- Task Slide-Over Modal --}}
<div class="viso-backdrop" id="taskBackdrop"></div>
<div class="viso-slide-over" id="taskSlideOver">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
        <div class="d-flex align-items-center gap-2 text-muted small">
            <span id="taskModalProject">Project</span>
            <i class="icon-chevron-right" style="font-size:14px"></i>
            <span id="taskModalTitle" class="text-truncate" style="max-width:200px">Task Title</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small" id="taskModalDate"></span>
            <button onclick="VisoApp.closeTaskModal()" class="btn btn-light btn-sm rounded-circle p-2">
                <i class="icon-x" style="font-size:20px"></i>
            </button>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row flex-grow-1 overflow-hidden">
        {{-- Left Column: Details --}}
        <div class="flex-grow-1 overflow-auto p-4 border-end viso-scroll" id="taskDetailBody">
            {{-- Title --}}
            <h2 class="h4 fw-bold text-dark mb-4" id="taskDetailTitle"></h2>

            {{-- Quick Action Buttons --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button class="btn btn-light border d-flex align-items-center gap-2 btn-sm text-muted">
                    <i class="icon-user" style="font-size:14px"></i> Assignee
                </button>
                <button class="btn btn-light border d-flex align-items-center gap-2 btn-sm text-muted">
                    <i class="icon-calendar" style="font-size:14px"></i> Due Date
                </button>
                <button class="btn btn-light border d-flex align-items-center gap-2 btn-sm text-muted">
                    <i class="icon-clock" style="font-size:14px"></i> Estimation
                </button>
            </div>

            {{-- Status & Priority --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                    <select id="taskStatusSelect" class="form-select form-select-sm bg-light border-0 fw-medium"
                            onchange="VisoApp.updateTaskField('status', this.value)">
                        <option>Todo</option>
                        <option>Scheduled</option>
                        <option>In Progress</option>
                        <option>Under Review</option>
                        <option>Completed</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase">Priority</label>
                    <select id="taskPrioritySelect" class="form-select form-select-sm bg-light border-0 fw-medium"
                            onchange="VisoApp.updateTaskField('priority', this.value)">
                        <option>Low</option>
                        <option>Normal</option>
                        <option>High</option>
                        <option>Urgent</option>
                    </select>
                </div>
            </div>

            {{-- Recurrence --}}
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-2">
                    <i class="icon-repeat" style="font-size:14px"></i> Recurrence
                </label>
                <select id="taskRecurrenceSelect" class="form-select form-select-sm bg-light border-0 fw-medium"
                        onchange="VisoApp.updateTaskField('recurrence', this.value)">
                    <option value="none">None</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>

            {{-- Subtasks --}}
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h3 class="h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                        <i class="icon-check-square" style="font-size:16px"></i> Subtasks
                    </h3>
                    <span class="small text-muted" id="subtaskCount">0/0</span>
                </div>
                <div class="progress mb-3" style="height:6px">
                    <div class="progress-bar" id="subtaskProgress" style="width:0%"></div>
                </div>
                <div class="d-flex flex-column gap-2" id="subtaskList"></div>

                {{-- Add Subtask --}}
                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded mt-2">
                    <i class="icon-check-square text-muted" style="font-size:14px"></i>
                    <input type="text" id="newSubtaskInput" placeholder="Add subtask and press Enter..."
                           class="form-control form-control-sm border-0 bg-transparent"
                           onkeydown="if(event.key==='Enter') VisoApp.addSubtask(this.value)">
                </div>
            </div>
        </div>

        {{-- Right Column: Chat / Comments --}}
        <div class="bg-light d-flex flex-column" style="width:320px;min-width:320px">
            <div class="p-3 border-bottom bg-white">
                <h3 class="h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                    <i class="icon-message-square" style="font-size:16px"></i> Comments
                </h3>
            </div>
            <div class="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-3 viso-scroll" id="chatMessages"></div>
            <div class="p-3 bg-white border-top">
                <form id="chatForm" onsubmit="event.preventDefault(); VisoApp.sendChatMessage();" class="position-relative">
                    <input type="text" id="chatInput" placeholder="Write a comment..."
                           class="form-control form-control-sm pe-5">
                    <button type="submit" class="btn btn-link btn-sm position-absolute top-50 end-0 translate-middle-y text-primary">
                        <i class="icon-send" style="font-size:16px"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
