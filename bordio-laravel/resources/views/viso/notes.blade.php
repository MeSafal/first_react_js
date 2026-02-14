@extends('layouts.app')
@section('title', 'Notes')

@section('content')
<div class="container-fluid py-4 h-100" style="max-width:1200px">
    <div class="card h-100 border shadow-sm overflow-hidden d-flex flex-row">
        {{-- Notes List Sidebar --}}
        <div class="bg-light border-end d-flex flex-column" style="width:300px;min-width:300px">
            <div class="p-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h6 fw-bold text-secondary d-flex align-items-center gap-2 mb-0">
                        <i class="icon-file-text text-muted" style="font-size:18px"></i> Notes
                    </h2>
                    <button class="btn btn-sm btn-light border shadow-sm text-muted" onclick="VisoApp.addNote()">
                        <i class="icon-plus" style="font-size:16px"></i>
                    </button>
                </div>
                <div class="position-relative">
                    <i class="icon-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size:14px"></i>
                    <input type="text" placeholder="Search..." class="form-control form-control-sm ps-4"
                           oninput="VisoApp.filterNotes(this.value)">
                </div>
            </div>

            <div class="flex-grow-1 overflow-auto viso-scroll" id="notesList">
                @foreach($notes as $note)
                    @php
                        $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                        $noteColor = $note->color ?? $colors[($loop->index) % count($colors)];
                    @endphp
                    <div class="viso-note-item {{ $loop->first ? 'active' : '' }}"
                         data-note-id="{{ $note->id }}" onclick="VisoApp.selectNote({{ $note->id }})">
                        <h6 class="small fw-bold mb-1 {{ $loop->first ? 'text-primary' : 'text-dark' }}">{{ $note->title }}</h6>
                        <p class="small text-muted text-truncate mb-2 fs-11">{{ $note->preview ?? Str::limit($note->content, 60) }}</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted fs-10">{{ $note->updated_at->format('M j') }}</span>
                            <div class="rounded-circle {{ $noteColor }}" style="width:8px;height:8px"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Editor Area --}}
        <div class="flex-grow-1 d-flex flex-column bg-white" id="noteEditor">
            @if($notes->count())
                @php $firstNote = $notes->first(); @endphp
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="small text-muted" id="noteLastEdited">Last edited {{ $firstNote->updated_at->format('g:i A') }}</div>
                    <div class="dropdown">
                        <button class="btn btn-link btn-sm text-muted p-0" data-bs-toggle="dropdown">
                            <i class="icon-more-horizontal" style="font-size:20px"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item small text-danger" href="#" onclick="event.preventDefault(); VisoApp.deleteNote()">
                                <i class="icon-trash-2 me-2" style="font-size:14px"></i> Delete Note
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="flex-grow-1 p-4 overflow-auto viso-scroll">
                    <h1 class="h2 fw-bold text-dark mb-4" contenteditable="true" id="noteTitleEdit"
                        onblur="VisoApp.saveNoteField('title', this.textContent)">{{ $firstNote->title }}</h1>
                    <div class="text-secondary" contenteditable="true" id="noteContentEdit"
                         onblur="VisoApp.saveNoteField('content', this.innerHTML)">
                        {!! $firstNote->content ?: '<p>Start typing your note here...</p>' !!}
                    </div>
                </div>
            @else
                <div class="flex-grow-1 d-flex align-items-center justify-content-center text-muted fst-italic">
                    No notes yet. Click <strong class="ms-1">+</strong> to create one.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
