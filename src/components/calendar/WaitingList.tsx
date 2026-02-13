import React from 'react';
import { useBordioStore } from '../../store/useBordioStore';
import { Task } from '../../types';
import { useDraggable } from '@dnd-kit/core';
import { clsx } from 'clsx';
import { GripVertical } from 'lucide-react';

const DraggableTaskCard = ({ task }: { task: Task }) => {
    const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({
        id: task.id,
        data: { task, type: 'waiting-task' },
    });

    const style = transform ? {
        transform: `translate3d(${transform.x}px, ${transform.y}px, 0)`,
    } : undefined;

    return (
        <div
            ref={setNodeRef}
            style={style}
            {...listeners}
            {...attributes}
            className={clsx(
                "card mb-2 border shadow-sm cursor-grab active-cursor-grabbing user-select-none transition-all",
                isDragging ? "opacity-50 border-primary" : "bg-white border-light"
            )}
        >
            <div className="card-body p-2 d-flex align-items-start gap-2">
                <GripVertical size={16} className="text-secondary mt-1" />
                <div className="flex-grow-1 min-w-0">
                    <h6 className="card-title text-dark small fw-medium text-truncate mb-1">{task.title}</h6>
                    <div className="d-flex align-items-center gap-2">
                        <span className={clsx(
                            "badge rounded-pill fw-normal",
                            task.priority === 'Urgent' ? "bg-danger bg-opacity-10 text-danger" : "bg-light text-secondary"
                        )} style={{ fontSize: '10px' }}>
                            {task.priority}
                        </span>
                        <span className="small text-muted" style={{ fontSize: '10px' }}>{task.timeEstimate}m</span>
                    </div>
                </div>
            </div>
        </div>
    );
};

export const WaitingList = () => {
    const { tasks } = useBordioStore();
    const waitingTasks = tasks.filter(t => !t.dueDate && t.status !== 'Completed');

    return (
        <aside className="bg-light border-start d-flex flex-column h-100" style={{ width: '280px', minWidth: '280px' }}>
            <div className="p-3 border-bottom bg-white">
                <h6 className="fw-bold text-dark mb-1">Waiting List</h6>
                <p className="small text-muted mb-0" style={{ fontSize: '11px' }}>Drag tasks to the calendar</p>
            </div>
            <div className="flex-grow-1 overflow-auto p-2 custom-scrollbar">
                {waitingTasks.map(task => (
                    <DraggableTaskCard key={task.id} task={task} />
                ))}
                {waitingTasks.length === 0 && (
                    <div className="text-center p-4 text-muted small fst-italic">
                        No unscheduled tasks.
                    </div>
                )}
            </div>
        </aside>
    );
};
