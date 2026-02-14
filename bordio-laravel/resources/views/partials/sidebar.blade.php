@php
    $currentRoute = request()->route()?->getName() ?? '';
    $currentPath = request()->path();
    $user = auth()->user();

    $navItems = [
        ['route' => 'my-work',        'icon' => 'check-square',  'label' => 'My Work'],
        ['route' => 'kanban',         'icon' => 'layout-grid',   'label' => 'Kanban Board'],
        ['route' => 'calendar',       'icon' => 'calendar-days', 'label' => 'Calendar'],
        ['route' => 'notes',          'icon' => 'file-text',     'label' => 'Notes'],
        ['route' => 'team-workload',  'icon' => 'users',         'label' => 'Team Workload'],
    ];
@endphp

<div class="viso-sidebar" id="visoSidebar">
    {{-- Logo --}}
    <div class="p-3 mb-2">
        <div class="d-flex align-items-center gap-2 text-white cursor-pointer">
            <div class="bg-primary rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px">
                <span class="fw-bold small text-white">V</span>
            </div>
            <div>
                <div class="fw-bold small text-white">Visobotics</div>
                <i class="icon-chevron-down text-secondary" style="font-size:14px"></i>
            </div>
        </div>
    </div>

    {{-- Main Navigation --}}
    <div class="d-flex flex-column gap-0 mb-3">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="viso-sidebar-item {{ $currentRoute === $item['route'] || ($item['route'] === 'my-work' && $currentRoute === 'dashboard') ? 'active' : '' }}">
                <i class="icon-{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Teams & Projects --}}
    <div class="flex-grow-1 overflow-auto viso-scroll px-1">
        <div class="viso-sidebar-section-title d-flex align-items-center justify-content-between"
             data-bs-toggle="collapse" data-bs-target="#sidebarProjects">
            <span>Teams & Projects</span>
            <i class="icon-chevron-down" style="font-size:14px"></i>
        </div>

        <div class="collapse show" id="sidebarProjects">
            @php $teams = \App\Models\Team::with('projects')->get(); @endphp
            @foreach($teams as $team)
                <div class="px-3 py-1 text-white text-opacity-50 small d-flex align-items-center gap-2 mt-2">
                    <i class="icon-folder" style="font-size:14px"></i> {{ $team->name }}
                </div>
                @foreach($team->projects as $project)
                    <a href="{{ route('project.view', $project) }}"
                       class="viso-sidebar-item {{ $currentPath === 'projects/'.$project->id ? 'active' : '' }}">
                        <i class="icon-briefcase" style="font-size:14px"></i>
                        <span class="text-truncate">{{ $project->name }}</span>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- User Footer --}}
    @if($user)
    <div class="mt-auto p-3 border-top" style="border-color: rgba(255,255,255,0.1) !important">
        <div class="d-flex align-items-center gap-2 text-white">
            <div class="position-relative">
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=3b82f6&color=fff' }}"
                     alt="{{ $user->name }}" class="rounded-circle" width="36" height="36">
                <span class="viso-online-dot"></span>
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-medium small text-truncate">{{ $user->name }}</div>
                <div class="text-white text-opacity-50 fs-11">{{ $user->role ?? 'Member' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-link text-white text-opacity-50 p-0" title="Logout">
                    <i class="icon-log-out" style="font-size:18px"></i>
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
