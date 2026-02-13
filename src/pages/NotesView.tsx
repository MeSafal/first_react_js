import React, { useState } from 'react';
import { FileText, Plus, MoreHorizontal, Search } from 'lucide-react';
import { clsx } from 'clsx';
import { format } from 'date-fns';

const NOTES_DATA = [
    { id: 'n1', title: 'Q4 Marketing Strategy', preview: 'Focus on social media ads...', date: new Date(), color: 'bg-primary' },
    { id: 'n2', title: 'Meeting Minutes', preview: 'Attendees: Alice, Bob...', date: new Date(Date.now() - 86400000), color: 'bg-success' },
    { id: 'n3', title: 'Design System Ideas', preview: 'Explore glassmorphism...', date: new Date(Date.now() - 172800000), color: 'bg-info' },
];

export const NotesView = () => {
    const [notes, setNotes] = useState(NOTES_DATA);
    const [activeNoteId, setActiveNoteId] = useState('n1');
    const activeNote = notes.find(n => n.id === activeNoteId);

    return (
        <div className="container-fluid py-4 h-100" style={{ maxWidth: '1200px' }}>
            <div className="card h-100 border shadow-sm overflow-hidden d-flex flex-row">
                {/* Sidebar List */}
                <div className="bg-light border-end d-flex flex-column" style={{ width: '300px', minWidth: '300px' }}>
                    <div className="p-3 border-bottom">
                        <div className="d-flex align-items-center justify-content-between mb-3">
                            <h2 className="h6 fw-bold text-secondary d-flex align-items-center gap-2 mb-0">
                                <FileText size={18} className="text-muted" /> Notes
                            </h2>
                            <button
                                onClick={handleAddNote}
                                className="btn btn-sm btn-light border shadow-sm text-muted"
                            >
                                <Plus size={16} />
                            </button>
                        </div>
                        <div className="position-relative">
                            <Search size={14} className="position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" />
                            <input
                                type="text"
                                placeholder="Search..."
                                className="form-control form-control-sm ps-4"
                            />
                        </div>
                    </div>

                    <div className="flex-grow-1 overflow-auto">
                        {notes.map(note => (
                            <div
                                key={note.id}
                                onClick={() => setActiveNoteId(note.id)}
                                className={clsx(
                                    "p-3 cursor-pointer border-bottom transition-colors hover-bg-white",
                                    activeNoteId === note.id ? "bg-white border-start border-4 border-primary" : "border-start border-4 border-transparent"
                                )}
                            >
                                <h6 className={clsx("small fw-bold mb-1", activeNoteId === note.id ? "text-primary" : "text-dark")}>{note.title}</h6>
                                <p className="small text-muted text-truncate mb-2" style={{ fontSize: '11px' }}>{note.preview}</p>
                                <div className="d-flex align-items-center justify-content-between">
                                    <span className="text-muted" style={{ fontSize: '10px' }}>{format(note.date, 'MMM d')}</span>
                                    <div className={clsx("rounded-circle", note.color)} style={{ width: '8px', height: '8px' }} />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Editor Area */}
                <div className="flex-grow-1 d-flex flex-column bg-white">
                    {activeNote ? (
                        <>
                            <div className="p-3 border-bottom d-flex align-items-center justify-content-between">
                                <div className="small text-muted">Last edited {format(activeNote.date, 'h:mm a')}</div>
                                <button className="btn btn-link btn-sm text-muted p-0">
                                    <MoreHorizontal size={20} />
                                </button>
                            </div>
                            <div className="flex-grow-1 p-4 overflow-auto">
                                <h1 className="h2 fw-bold text-dark mb-4 outline-none" contentEditable suppressContentEditableWarning>
                                    {activeNote.title}
                                </h1>
                                <div className="text-secondary">
                                    <p contentEditable suppressContentEditableWarning>
                                        Start typing your note here... This is a simulated rich text editor area.
                                    </p>
                                    <ul className="ps-3 mt-3">
                                        <li>Click to edit this text</li>
                                        <li>Supports basic simulated typing</li>
                                        <li>Integrated with the design system</li>
                                    </ul>
                                </div>
                            </div>
                        </>
                    ) : (
                        <div className="flex-grow-1 d-flex align-items-center justify-content-center text-muted fst-italic">
                            Select a note to view
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};
