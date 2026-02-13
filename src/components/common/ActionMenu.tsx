import React, { useState, useRef, useEffect } from 'react';
import { MoreVertical, Edit, Copy, Trash2, FolderInput } from 'lucide-react';

interface ActionMenuProps {
    onEdit?: () => void;
    onDuplicate?: () => void;
    onMove?: () => void;
    onDelete?: () => void;
}

export const ActionMenu: React.FC<ActionMenuProps> = ({ onEdit, onDuplicate, onMove, onDelete }) => {
    const [isOpen, setIsOpen] = useState(false);
    const menuRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        if (isOpen) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [isOpen]);

    return (
        <div className="position-relative" ref={menuRef}>
            <button
                onClick={(e) => {
                    e.stopPropagation();
                    setIsOpen(!isOpen);
                }}
                className="btn btn-sm btn-link text-muted p-1"
            >
                <MoreVertical size={16} />
            </button>

            {isOpen && (
                <div
                    className="position-absolute end-0 bg-white border shadow-lg rounded mt-1"
                    style={{ minWidth: '180px', zIndex: 1000 }}
                >
                    <div className="py-1">
                        {onEdit && (
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onEdit();
                                    setIsOpen(false);
                                }}
                                className="w-100 text-start btn btn-sm btn-link text-dark text-decoration-none d-flex align-items-center gap-2 px-3 py-2 hover-bg-light"
                            >
                                <Edit size={14} />
                                <span>Edit</span>
                            </button>
                        )}
                        {onDuplicate && (
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onDuplicate();
                                    setIsOpen(false);
                                }}
                                className="w-100 text-start btn btn-sm btn-link text-dark text-decoration-none d-flex align-items-center gap-2 px-3 py-2 hover-bg-light"
                            >
                                <Copy size={14} />
                                <span>Duplicate</span>
                            </button>
                        )}
                        {onMove && (
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onMove();
                                    setIsOpen(false);
                                }}
                                className="w-100 text-start btn btn-sm btn-link text-dark text-decoration-none d-flex align-items-center gap-2 px-3 py-2 hover-bg-light"
                            >
                                <FolderInput size={14} />
                                <span>Move to Project</span>
                            </button>
                        )}
                        {onDelete && (
                            <>
                                <hr className="my-1" />
                                <button
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        onDelete();
                                        setIsOpen(false);
                                    }}
                                    className="w-100 text-start btn btn-sm btn-link text-danger text-decoration-none d-flex align-items-center gap-2 px-3 py-2 hover-bg-light"
                                >
                                    <Trash2 size={14} />
                                    <span>Delete</span>
                                </button>
                            </>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
};
