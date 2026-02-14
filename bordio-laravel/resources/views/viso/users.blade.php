@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div class="container-fluid py-4" style="max-width:1000px">
    {{-- Header --}}
    <header class="d-flex align-items-center justify-content-between mb-5 viso-slide-up">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">Team Members</h1>
            <p class="text-muted mb-0">{{ $users->count() }} members across {{ $teams->count() }} teams</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="icon-user-plus" style="font-size:18px"></i>
                <span class="d-none d-md-inline">Add Member</span>
            </button>
        </div>
    </header>

    {{-- Users Grid --}}
    <div class="row g-4">
        @foreach($users as $user)
        <div class="col-md-6 col-lg-4 viso-fade-in">
            <div class="card h-100 border-0 shadow-sm transition-all hover-shadow">
                <div class="card-body text-center p-4">
                    {{-- Avatar --}}
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=80&background=3b82f6&color=fff' }}"
                         alt="{{ $user->name }}" class="rounded-circle shadow-sm mb-3" width="72" height="72">

                    <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small mb-2">{{ $user->email }}</p>

                    <span class="badge {{ $user->role === 'Super Admin' ? 'bg-primary' : ($user->role === 'Admin' ? 'bg-info' : 'bg-secondary bg-opacity-25 text-secondary') }} rounded-pill mb-3">
                        {{ $user->role ?? 'Member' }}
                    </span>

                    {{-- Quick stats --}}
                    @php
                        $taskCount = $user->tasks()->count() ?? 0;
                    @endphp
                    <div class="d-flex justify-content-center gap-4 text-muted small mt-1 mb-3">
                        <div>
                            <span class="fw-bold text-dark d-block">{{ $taskCount }}</span>
                            Tasks
                        </div>
                        <div>
                            <span class="fw-bold text-dark d-block">{{ $user->projects()->count() ?? 0 }}</span>
                            Projects
                        </div>
                    </div>

                    {{-- Actions --}}
                    @if($user->id !== auth()->id())
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-light btn-sm border text-muted" title="Edit"
                                onclick="VisoApp.editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ addslashes($user->role) }}')">
                            <i class="icon-edit-2" style="font-size:14px"></i>
                        </button>
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
                              onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-light btn-sm border text-danger" title="Delete">
                                <i class="icon-trash-2" style="font-size:14px"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <span class="badge bg-primary bg-opacity-10 text-primary small">You</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="icon-user-plus text-primary me-2" style="font-size:20px"></i>
                        Add New Member
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Jane Doe" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="jane@company.com" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Role</label>
                            <input type="text" name="role" class="form-control" placeholder="e.g. Designer, Developer" value="Member">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Min 6 chars" value="password">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Assign to Teams</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($teams as $team)
                                <label class="d-flex align-items-center gap-2 px-3 py-2 border rounded cursor-pointer hover-bg-light">
                                    <input type="checkbox" name="team_ids[]" value="{{ $team->id }}" class="form-check-input mt-0">
                                    <span class="small">{{ $team->name }}</span>
                                    <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill fs-10">{{ $team->members->count() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-check me-1" style="font-size:16px"></i> Add Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" id="editUserForm">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="icon-edit-2 text-primary me-2" style="font-size:20px"></i>
                        Edit Member
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                        <input type="text" name="name" id="editUserName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Email</label>
                        <input type="email" name="email" id="editUserEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Role</label>
                        <input type="text" name="role" id="editUserRole" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => VisoApp.toast('{{ session('success') }}'));</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded', () => VisoApp.toast('{{ session('error') }}', 'danger'));</script>
@endif
@endsection
