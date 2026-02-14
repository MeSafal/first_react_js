@extends('layouts.app')
@section('title', 'Notes')

@section('content')
<div class="d-flex h-100 overflow-hidden">
    {{-- Sidebar List --}}
    <div class="d-flex flex-column border-end bg-white h-100" style="width:320px;min-width:320px">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between viso-slide-up">
            <h1 class="h5 fw-bold text-dark mb-0">Notes</h1>
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="VisoApp.addNote()">
                <i class="icon-plus"></i> New
            </button>
        </div>
        <div class="p-3 border-bottom bg-light">
            <div class="position-relative">
                <i class="icon-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                <input type="text" class="form-control form-control-sm ps-5 border-0 shadow-sm"
                       placeholder="Search notes..." onkeyup="VisoApp.filterNotes(this.value)">
            </div>
        </div>
        <div class="flex-grow-1 overflow-auto viso-scroll">
            @forelse($notes as $note)
                <div class="viso-note-item d-flex flex-column gap-1 {{ $loop->first ? 'active' : '' }}"
                     data-note-id="{{ $note->id }}"
                     onclick="VisoApp.selectNote({{ $note->id }})">
                    <h6 class="fw-bold mb-0 text-truncate {{ $loop->first ? 'text-primary' : 'text-dark' }}">{{ $note->title }}</h6>
                    <p class="text-muted small mb-0 text-truncate-2" style="font-size:12px">
                        {{ $note->preview ?? 'No additional text' }}
                    </p>
                    <span class="text-muted fs-10 mt-1">
                        {{ $note->updated_at->diffForHumans() }}
                    </span>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <p class="small fst-italic">No notes yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Main Editor --}}
    <div class="flex-grow-1 d-flex flex-column h-100 bg-white">
        @if($notes->count())
            {{-- Toolbar --}}
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-white viso-slide-up">
                <span class="text-muted small" id="noteLastEdited">
                    Last edited {{ $notes->first()->updated_at->format('M j, g:i a') }}
                </span>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light btn-sm border text-danger" onclick="VisoApp.deleteNote()">
                        <i class="icon-trash-2"></i>
                    </button>
                </div>
            </div>

            {{-- Editor --}}
            <div class="flex-grow-1 overflow-auto p-5 viso-scroll">
                <div class="mx-auto" style="max-width:720px">
                    <h1 class="display-6 fw-bold text-dark mb-4 border-bottom pb-2 outline-none"
                        contenteditable="true" id="noteTitleEdit"
                        onblur="VisoApp.saveNoteField('title', this.textContent)">
                        {{ $notes->first()->title }}
                    </h1>
                    <div class="lead text-dark outline-none" style="min-height:400px"
                         contenteditable="true" id="noteContentEdit"
                         onblur="VisoApp.saveNoteField('content', this.innerHTML)">
                        {!! $notes->first()->content !!}
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                <i class="icon-file-text mb-3" style="font-size:48px;opacity:0.2"></i>
                <h3 class="h5 fw-bold">Select a note or create a new one</h3>
                <button class="btn btn-primary mt-3" onclick="VisoApp.addNote()">Create Note</button>
            </div>
        @endif
    </div>
</div>
@endsection
