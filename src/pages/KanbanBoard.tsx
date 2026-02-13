import React, { useState } from 'react';
import {
    DndContext,
    DragOverlay,
    useDroppable,
    useDraggable,
    DragEndEvent,
    DragStartEvent,
    PointerSensor,
    useSensor,
    useSensors
} from '@dnd-kit/core';
import { useBordioStore } from '../store/useBordioStore';
import { Task, TaskStatus } from '../types';
import { clsx } from 'clsx';
import { MoreHorizontal, Plus } from 'lucide-react';

const KanbanCard = ({ task }: { task: Task }) => {
    const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({
        id: task.id,
        data: { task, type: 'kanban-card' },
    });

    const style = transform ? {
        transform: `translate3d(${transform.x}px, ${transform.y}px, 0)`,
    } : undefined;

    const { users } = useBordioStore();
    const assignees = users.filter(u => task.assigneeIds.includes(u.id));

    return (
        <div
            ref={setNodeRef}
            style={style}
            {...listeners}
            {...attributes}
            className={clsx(
                "card mb-3 border bg-white shadow-sm cursor-grab active-cursor-grabbing user-select-none",
                isDragging ? "opacity-50" : "opacity-100"
            )}
        >
            <div className="card-body p-3">
                <div className="d-flex justify-content-between align-items-start mb-2">
                    <span className={clsx(
                        "badge rounded-pill fw-normal",
                        task.priority === 'Urgent' ? "bg-danger bg-opacity-10 text-danger" : "bg-light text-secondary"
                    )}>
                        {task.priority}
                    </span>
                    <button className="btn btn-sm btn-link text-muted p-0 text-dark-hover">
                        <MoreHorizontal size={14} />
                    </button>
                </div>

                <h5 className="card-title h6 fw-bold text-dark mb-3">{task.title}</h5>

                <div className="d-flex align-items-center justify-content-between">
                    <div className="d-flex ms-1">
                        {assignees.map((user, i) => (
                            <img
                                key={user.id}
                                src={user.avatar}
                                alt={user.name}
                                className="rounded-circle border border-white"
                                width="24"
                                height="24"
                                style={{ marginLeft: i > 0 ? '-8px' : '0' }}
                                title={user.name}
                            />
                        ))}
                    </div>
                    <span className="small text-muted">{task.timeEstimate}m</span>
                </div>
            </div>
        </div>
    );
};

const KanbanColumn = ({ status, tasks }: { status: TaskStatus, tasks: Task[] }) => {
    const { setNodeRef, isOver } = useDroppable({
        id: status,
        data: { status },
    });

    return (
        <div className="d-flex flex-column h-100" style={{ minWidth: '280px', width: '280px' }}>
            {/* Header */}
            <div className="d-flex align-items-center justify-content-between mb-3 px-1">
                <div className="d-flex align-items-center gap-2">
                    <h6 className="fw-bold text-secondary mb-0">{status}</h6>
                    <span className="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{tasks.length}</span>
                </div>
                <button className="btn btn-sm btn-link text-muted p-0">
                    <Plus size={16} />
                </button>
            </div>

            {/* Drop Area */}
            <div
                ref={setNodeRef}
                className={clsx(
                    "flex-grow-1 rounded-3 p-2 transition-colors border border-2",
                    isOver ? "border-primary bg-primary bg-opacity-10" : "border-transparent bg-light"
                )}
            >
                {tasks.map(task => (
                    <KanbanCard key={task.id} task={task} />
                ))}
            </div>
        </div>
    );
};

export const KanbanBoard = () => {
    const { tasks, moveTask } = useBordioStore();
    const [activeDragTask, setActiveDragTask] = useState<Task | null>(null);

    const columns: TaskStatus[] = ['Todo', 'Scheduled', 'In Progress', 'Under Review', 'Completed'];

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: {
                distance: 8,
            },
        })
    );

    const handleDragStart = (event: DragStartEvent) => {
        if (event.active.data.current?.task) {
            setActiveDragTask(event.active.data.current.task);
        }
    };

    const handleDragEnd = (event: DragEndEvent) => {
        const { active, over } = event;

        if (over && active.data.current?.task) {
            const status = over.id as TaskStatus;
            const task = active.data.current.task as Task;

            if (task.status !== status) {
                moveTask(task.id, status);
            }
        }
        setActiveDragTask(null);
    };

    return (
        <DndContext sensors={sensors} onDragStart={handleDragStart} onDragEnd={handleDragEnd}>
            <div className="h-100 overflow-auto pb-3 custom-scrollbar px-4 pt-3">
                <div className="d-flex gap-4 h-100" style={{ width: 'max-content' }}>
                    {columns.map(status => (
                        <KanbanColumn
                            key={status}
                            status={status}
                            tasks={tasks.filter(t => t.status === status)}
                        />
                    ))}
                </div>
            </div>

            <DragOverlay>
                {activeDragTask ? (
                    <div className="card shadow-lg border-primary" style={{ width: '280px', transform: 'rotate(2deg)' }}>
                        <div className="card-body p-3">
                            <div className="d-flex justify-content-between align-items-start mb-2">
                                <span className="badge bg-light text-secondary rounded-pill fw-normal">
                                    {activeDragTask.priority}
                                </span>
                            </div>
                            <h5 className="card-title h6 fw-bold text-dark mb-0">{activeDragTask.title}</h5>
                        </div>
                    </div>
                ) : null}
            </DragOverlay>
        </DndContext>
    );
};
