<nav class="viso-sidebar viso-scroll">
    {{-- Brand --}}
    <div class="px-4 py-4 mb-2">
        <a href="{{ route('my-work') }}" class="d-flex align-items-center gap-3 text-decoration-none">
            <div class="viso-logo-icon">B</div>
            <span class="fw-bold fs-5 text-white tracking-wide">Bordio</span>
        </a>
    </div>

    {{-- Main Navigation --}}
    <div class="px-2 mb-4">
        <div class="viso-sidebar-section-title">Workspace</div>
        
        <a href="{{ route('my-work') }}" class="viso-sidebar-item {{ request()->routeIs('my-work') ? 'active' : '' }}">
            <i class="icon-check-circle"></i>
            <span>My Work</span>
        </a>
        
        <a href="{{ route('kanban') }}" class="viso-sidebar-item {{ request()->routeIs('kanban') ? 'active' : '' }}">
            <i class="icon-trello"></i>
            <span>Kanban Board</span>
        </a>
        
        <a href="{{ route('calendar') }}" class="viso-sidebar-item {{ request()->routeIs('calendar') ? 'active' : '' }}">
            <i class="icon-calendar"></i>
            <span>Calendar</span>
        </a>

        <a href="{{ route('team-workload') }}" class="viso-sidebar-item {{ request()->routeIs('team-workload') ? 'active' : '' }}">
            <i class="icon-users"></i>
            <span>Team Workload</span>
        </a>
    </div>

    {{-- Projects Section --}}
    <div class="px-2 mb-4 flex-grow-1">
        <div class="d-flex align-items-center justify-content-between pe-3">
            <div class="viso-sidebar-section-title">Projects</div>
            <button class="btn btn-sm p-0 text-white text-opacity-50 hover-text-white transition-all" 
                    data-bs-toggle="modal" data-bs-target="#createProjectModal" title="New Project">
                <i class="icon-plus" style="font-size:14px"></i>
            </button>
        </div>

        @php
            $teams = \App\Models\Team::with('projects')->get();
        @endphp

        @foreach($teams as $team)
            @if($team->projects->count() > 0)
                <div class="mt-2 mb-1 px-3 fs-10 text-muted fw-bold text-uppercase">{{ $team->name }}</div>
                @foreach($team->projects as $project)
                    @php
                        $color = match($loop->iteration % 4) {
                            1 => '#3b82f6', // blue
                            2 => '#ef4444', // red
                            3 => '#22c55e', // green
                            default => '#f59e0b', // yellow
                        };
                    @endphp
                    <a href="{{ route('projects.show', $project) }}" class="viso-sidebar-item ps-3 {{ request()->is('projects/'.$project->id) ? 'active' : '' }}">
                        <div class="viso-project-dot me-2" style="background: {{ $color }}"></div>
                        <span class="text-truncate flex-grow-1">{{ $project->name }}</span>
                    </a>
                @endforeach
            @endif
        @endforeach
    </div>

    {{-- Tools & Settings --}}
    <div class="px-2 mb-4">
        <div class="viso-sidebar-section-title">Tools</div>
        
        <a href="{{ route('notes') }}" class="viso-sidebar-item {{ request()->routeIs('notes') ? 'active' : '' }}">
            <i class="icon-file-text"></i>
            <span>Notes</span>
        </a>

        @if(auth()->user()->hasRole('Super Admin'))
        <a href="{{ route('users.index') }}" class="viso-sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="icon-settings"></i>
            <span>Manage Users</span>
        </a>
        @endif
    </div>

    {{-- User Footer --}}
    <div class="mt-auto px-4 py-3 border-top border-white border-opacity-10 d-flex align-items-center gap-3">
        <div class="position-relative">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=36&background=3b82f6&color=fff' }}" 
                 alt="User" class="rounded-circle border border-2 border-white border-opacity-25" width="36" height="36">
            <div class="viso-online-dot"></div>
        </div>
        <div class="flex-grow-1 overflow-hidden">
            <div class="fw-bold text-white fs-11 text-truncate">{{ auth()->user()->name }}</div>
            <div class="text-muted fs-10 text-truncate">{{ auth()->user()->email }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
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
                        <label class="form-label small fw-bold text-muted text-uppercase">Initial Members</label>
                        <select name="member_ids[]" class="form-select" multiple size="3">
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text fs-10">Hold Ctrl/Cmd to select multiple</div>
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
