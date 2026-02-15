<div class="modal fade" id="quickAddTaskModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light bg-opacity-50 border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3">
                        <i class="icon-zap text-warning" style="font-size:20px"></i>
                    </div>
                    Quick Add Task
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Task Title</label>
                    <input type="text" id="quickTaskTitle" class="form-control form-control-lg border-0 bg-light shadow-none px-3" 
                           placeholder="What needs to be done?" autofocus 
                           style="border-radius: 10px; font-size: 1.1rem; font-weight: 500;">
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                            <i class="icon-folder me-1" style="font-size:12px"></i> Project
                        </label>
                        <select id="quickTaskProject" class="form-select border-0 bg-light shadow-none" 
                                onchange="VisoApp.filterQuickTaskMembers(this)" style="border-radius: 8px;">
                            <option value="">Personal (No project)</option>
                            @php $projects = \App\Models\Project::with('members')->get(); @endphp
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" data-members="{{ $p->members->pluck('id') }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                            <i class="icon-flag me-1" style="font-size:12px"></i> Priority
                        </label>
                        <select id="quickTaskPriority" class="form-select border-0 bg-light shadow-none" style="border-radius: 8px;">
                            <option value="Normal" selected>Normal</option>
                            <option value="Low">Low</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4 pt-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                            <i class="icon-calendar me-1" style="font-size:12px"></i> Due Date
                        </label>
                        <input type="date" id="quickTaskDue" class="form-control border-0 bg-light shadow-none" style="border-radius: 8px;">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider d-flex justify-content-between">
                            <span><i class="icon-users me-1" style="font-size:12px"></i> Assign To</span>
                            <span class="text-lowercase fw-normal opacity-50" style="font-size:10px">Select multiple members</span>
                        </label>
                        
                        {{-- Hidden Select --}}
                        @php $users = \App\Models\User::all(); @endphp
                        <select id="quickTaskAssignee" class="d-none" multiple>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>

                        {{-- Chips Container --}}
                        <div class="d-flex flex-wrap gap-2 p-2 bg-light rounded-3" id="quickTaskAssigneeList" 
                             style="max-height: 150px; overflow-y: auto; min-height: 42px;">
                            @foreach($users as $u)
                                @php $isMe = $u->id === auth()->id(); @endphp
                                <div class="viso-assignee-chip quick-task-chip cursor-pointer transition-all {{ $isMe ? 'selected border-primary bg-primary bg-opacity-10' : 'bg-white' }}" 
                                     data-user-id="{{ $u->id }}"
                                     onclick="VisoApp.toggleQuickTaskMember({{ $u->id }})"
                                     style="border: 1px solid transparent;">
                                    <img src="{{ $u->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&size=22&background=3b82f6&color=fff' }}" 
                                         class="rounded-2" width="22" height="22">
                                    <span class="small fw-medium">{{ $u->name }}</span>
                                    <span class="remove-btn {{ $isMe ? '' : 'd-none' }} ms-1">
                                        <i class="icon-x" style="font-size:12px; vertical-align: middle;"></i>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                <button type="button" class="btn btn-primary fw-bold px-4" onclick="VisoApp.quickAddTask()" style="border-radius: 8px;">
                    <i class="icon-plus me-1" style="font-size:16px"></i> Create Task
                </button>
            </div>
        </div>
    </div>
</div>
