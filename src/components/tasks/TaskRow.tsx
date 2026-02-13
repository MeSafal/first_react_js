import React from 'react';
import { useBordioStore } from '../../store/useBordioStore';
import { Task } from '../../types';
import { format } from 'date-fns';
import { CheckCircle2, Circle, Clock, Repeat } from 'lucide-react';
import { clsx } from 'clsx';

interface TaskRowProps {
    task: Task;
    onClick: () => void;
}

export const TaskRow = ({ task, onClick }: TaskRowProps) => {
    const { users } = useBordioStore();
    const assignees = users.filter(u => task.assigneeIds.includes(u.id));

    return (
        <div
            onClick={onClick}
            className="d-flex align-items-center gap-3 p-3 bg-white border rounded shadow-sm hover-shadow-sm transition-all cursor-pointer"
            style={{ transition: 'background-color 0.2s, box-shadow 0.2s' }}
        >
            {/* Status Icon */}
            <div className={clsx(
                "d-flex align-items-center justify-content-center",
                task.status === 'Completed' ? "text-success" : "text-muted"
            )}>
                {task.status === 'Completed' ? <CheckCircle2 size={20} /> : <Circle size={20} />}
            </div>

            {/* Title */}
            <div className={clsx(
                "flex-grow-1 fw-medium",
                task.status === 'Completed' ? "text-decoration-line-through text-muted" : "text-dark"
            )}>
                {task.title}
            </div>

            {/* Meta Info */}
            <div className="d-flex align-items-center gap-3">
                {/* Due Date */}
                {task.dueDate && (
                    <div className={clsx(
                        "d-flex align-items-center gap-1 small",
                        new Date(task.dueDate) < new Date() && task.status !== 'Completed' ? "text-danger fw-bold" : "text-muted"
                    )}>
                        <Clock size={14} />
                        <span>{format(new Date(task.dueDate), 'MMM d')}</span>
                    </div>
                )}

                {/* Recurrence Indicator */}
                {task.recurrence && task.recurrence !== 'none' && (
                    <div className="d-flex align-items-center gap-1 text-primary" title={`Repeats ${task.recurrence}`}>
                        <Repeat size={14} />
                    </div>
                )}

                {/* Assignees */}
                <div className="d-flex align-items-center ps-2">
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
            </div>
        </div>
    );
};
