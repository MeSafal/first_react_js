<nav class="viso-sidebar">
    {{-- Brand & Toggle --}}
    <div class="viso-sidebar-header">
        <a href="{{ route('my-work') }}" class="d-flex align-items-center gap-3 text-decoration-none viso-brand-text">
            <img src="{{ asset('img/logo/logo_secondary.png') }}" alt="Visobotics" style="height: 32px; width: auto;">
            <span class="fw-bold fs-5 text-white tracking-wide">Visobotics</span>
        </a>
        <button class="btn btn-link p-0 text-white text-opacity-50 hover-text-white transition-all sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
            <i class="icon-arrow-left" style="font-size:16px"></i>
        </button>
    </div>

    {{-- Scrollable Sidebar Body --}}
    <div class="viso-sidebar-body viso-scroll">
        {{-- Main Navigation --}}
        <div class="px-2 mb-4">
            <div class="viso-sidebar-section-title">Workspace</div>
            
            <a href="{{ route('my-work') }}" class="viso-sidebar-item {{ request()->routeIs('my-work') ? 'active' : '' }}">
                <i class="icon-check"></i>
                <span>My Work</span>
                <span class="viso-sidebar-tooltip">My Work</span>
            </a>
            
            <a href="{{ route('kanban') }}" class="viso-sidebar-item {{ request()->routeIs('kanban') ? 'active' : '' }}">
                <i class="icon-layers"></i>
                <span>Kanban Board</span>
                <span class="viso-sidebar-tooltip">Kanban Board</span>
            </a>
            
            <a href="{{ route('calendar') }}" class="viso-sidebar-item {{ request()->routeIs('calendar') ? 'active' : '' }}">
                <i class="icon-calendar"></i>
                <span>Calendar</span>
                <span class="viso-sidebar-tooltip">Calendar</span>
            </a>

            <a href="{{ route('team-workload') }}" class="viso-sidebar-item {{ request()->routeIs('team-workload') ? 'active' : '' }}">
                <i class="icon-people"></i>
                <span>Team Workload</span>
                <span class="viso-sidebar-tooltip">Team Workload</span>
            </a>
        </div>

        {{-- Projects Section --}}
        <div class="px-2 mb-4 flex-grow-1">
            <div class="d-flex align-items-center justify-content-between pe-3 mb-1">
                <div class="viso-sidebar-section-title d-flex align-items-center gap-2">
                    Projects
                    <i class="icon-plus text-white text-opacity-50 cursor-pointer hover-text-white" 
                       style="font-size: 14px; margin-top: 2px;"
                       data-bs-toggle="modal" data-bs-target="#createProjectModal" title="New Project"></i>
                </div>
                <button class="btn btn-sm p-0 text-white text-opacity-50 hover-text-white transition-all viso-brand-text" 
                        data-bs-toggle="modal" data-bs-target="#createProjectModal" title="New Project">
                    <i class="icon-plus-circle" style="font-size:16px"></i>
                </button>
            </div>

            <a href="{{ route('projects.index') }}" class="viso-sidebar-item {{ request()->routeIs('projects.index') ? 'active' : '' }} mb-2">
                <i class="icon-grid"></i>
                <span>All Projects</span>
                <span class="viso-sidebar-tooltip">All Projects</span>
            </a>

            @php
                $teams = \App\Models\Team::with('projects')->get();
            @endphp

            <div class="d-flex flex-column gap-1">
                @foreach($teams as $team)
                    @if($team->projects->count() > 0)
                        <div class="mt-2 mb-1 px-3 d-flex align-items-center gap-2 opacity-50 viso-brand-text">
                            <div style="width: 4px; height: 1px; background: currentColor"></div>
                            <span class="fs-10 fw-bold text-uppercase tracking-wider">{{ $team->name }}</span>
                        </div>
                        @foreach($team->projects as $project)
                            @php
                                $color = match($loop->iteration % 4) {
                                    1 => '#3b82f6', // blue
                                    2 => '#10b981', // emerald
                                    3 => '#f59e0b', // amber
                                    default => '#ef4444', // red
                                };
                            @endphp
                            <a href="{{ route('projects.show', $project) }}" 
                               class="viso-sidebar-item py-1.5 {{ request()->is('projects/'.$project->id) ? 'active' : '' }}"
                               title="{{ $project->name }}">
                                <div class="viso-project-dot" style="background: {{ $color }}; width: 6px; height: 6px;"></div>
                                <span class="text-truncate flex-grow-1 small">{{ $project->name }}</span>
                                <span class="viso-sidebar-tooltip">{{ $project->name }}</span>
                            </a>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Tools & Settings --}}
        <div class="px-2 mb-4">
            <div class="viso-sidebar-section-title">Tools</div>
            
            <a href="{{ route('notes') }}" class="viso-sidebar-item {{ request()->routeIs('notes') ? 'active' : '' }}">
                <i class="icon-note"></i>
                <span>Notes</span>
                <span class="viso-sidebar-tooltip">Notes</span>
            </a>

            @if(auth()->user()->hasRole('Super Admin'))
            <a href="{{ route('users.index') }}" class="viso-sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="icon-settings"></i>
                <span>Manage Users</span>
                <span class="viso-sidebar-tooltip">Manage Users</span>
            </a>
            @endif
        </div>
    </div>

    {{-- User Footer --}}
    <div class="mt-auto px-4 py-3 border-top border-white border-opacity-10 d-flex align-items-center gap-3 sidebar-footer">
        <div class="position-relative">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=36&background=3b82f6&color=fff' }}" 
                 alt="User" class="rounded-circle border border-2 border-white border-opacity-25" width="28" height="28">
            <div class="viso-online-dot" style="width: 8px; height: 8px;"></div>
        </div>
        <div class="flex-grow-1 overflow-hidden viso-brand-text">
            <div class="fw-bold text-white fs-11 text-truncate">{{ auth()->user()->name }}</div>
            <div class="text-muted fs-10 text-truncate">{{ auth()->user()->email }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="viso-brand-text">
            @csrf
            <button type="submit" class="btn btn-link p-0 text-white text-opacity-50 hover-text-white" title="Logout">
                <i class="icon-log-out" style="font-size:16px"></i>
            </button>
        </form>
    </div>
</nav>

{{-- Create Project Modal --}}
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="icon-folder-plus text-primary me-2" style="font-size:20px"></i>
                        New Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Project Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Website Redesign" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Team</label>
                        <select name="team_id" class="form-select">
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Team Members</label>
                        
                        {{-- Hidden real input for form submission --}}
                        <select name="member_ids[]" id="realMemberSelect" class="d-none" multiple>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>{{ $user->id }}</option>
                            @endforeach
                        </select>

                        {{-- Filter Tabs --}}
                        <div class="d-flex gap-2 mb-3 overflow-auto pb-1" id="memberRoleFilters">
                            <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" onclick="filterProjectMembers('all', this)">All</button>
                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="filterProjectMembers('Marketing', this)">Marketing</button>
                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="filterProjectMembers('Design', this)">Design</button>
                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="filterProjectMembers('Development', this)">Development</button>
                        </div>

                        {{-- Member Chips List --}}
                        <div class="d-flex flex-wrap gap-2 mb-3" id="memberList" style="max-height: 300px; overflow-y: auto;">
                            @foreach(\App\Models\User::all() as $user)
                                @php
                                    $isSelected = $user->id === auth()->id();
                                    // Randomly assign roles for demo if not present, or use actual role
                                    $roles = ['Marketing', 'Design', 'Development'];
                                    $userRole = $user->role ?? $roles[$loop->index % 3]; 
                                @endphp
                                <div class="viso-assignee-chip user-chip {{ $isSelected ? 'selected border-primary bg-primary bg-opacity-10' : '' }}" 
                                     data-role="{{ $userRole }}" 
                                     data-user-id="{{ $user->id }}"
                                     onclick="toggleProjectMember({{ $user->id }})">
                                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=22&background=3b82f6&color=fff' }}" 
                                         class="rounded-circle" width="22" height="22">
                                    <span>{{ $user->name }}</span>
                                    <span class="remove-btn {{ $isSelected ? '' : 'd-none' }}">
                                        <i class="icon-x" style="font-size:12px"></i>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleProjectMember(userId) {
        const $select = $('#realMemberSelect');
        const $option = $select.find(`option[value="${userId}"]`);
        const isSelected = $option.prop('selected');
        
        // Toggle selection
        $option.prop('selected', !isSelected);
        
        // Update UI
        const $chip = $(`.user-chip[data-user-id="${userId}"]`);
        
        if (!isSelected) {
            // Become selected
            $chip.addClass('selected border-primary bg-primary bg-opacity-10');
            $chip.find('.remove-btn').removeClass('d-none');
        } else {
            // Become unselected
            $chip.removeClass('selected border-primary bg-primary bg-opacity-10');
            $chip.find('.remove-btn').addClass('d-none');
        }
    }

    function filterProjectMembers(role, btn) {
        // Update tabs
        $('#memberRoleFilters .btn').removeClass('btn-dark').addClass('btn-light');
        $(btn).removeClass('btn-light').addClass('btn-dark');

        // Filter chips
        if (role === 'all') {
            $('.user-chip').show();
        } else {
            $('.user-chip').each(function() {
                const itemRole = $(this).data('role');
                // Simple partial match or exact match
                if (itemRole === role) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    }
</script>
@endpush
