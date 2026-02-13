import React from 'react';
import { useBordioStore } from '../store/useBordioStore';
import { addDays, format, startOfWeek, isSameDay } from 'date-fns';
import { clsx } from 'clsx';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export const TeamWorkloadView = () => {
    const { users, tasks } = useBordioStore();
    const [startDate, setStartDate] = React.useState(startOfWeek(new Date(), { weekStartsOn: 1 }));

    const days = Array.from({ length: 7 }).map((_, i) => addDays(startDate, i));

    return (
        <div className="container-fluid py-4 h-100" style={{ maxWidth: '1200px' }}>
            <div className="card h-100 shadow-sm border-0 d-flex flex-column overflow-hidden">
                {/* Header */}
                <div className="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
                    <h5 className="mb-0 fw-bold text-dark">Team Workload</h5>
                    <div className="d-flex align-items-center gap-3">
                        <span className="small fw-medium text-muted">{format(startDate, 'MMM d')} - {format(addDays(startDate, 6), 'MMM d, yyyy')}</span>
                        <div className="btn-group">
                            <button onClick={() => setStartDate(addDays(startDate, -7))} className="btn btn-light btn-sm border"><ChevronLeft size={16} /></button>
                            <button onClick={() => setStartDate(addDays(startDate, 7))} className="btn btn-light btn-sm border"><ChevronRight size={16} /></button>
                        </div>
                    </div>
                </div>

                {/* Grid */}
                <div className="flex-grow-1 overflow-auto custom-scrollbar">
                    <div className="d-flex flex-column" style={{ minWidth: '900px' }}>
                        {/* Date Header */}
                        <div className="d-flex border-bottom">
                            <div className="p-3 bg-light border-end text-center small fw-bold text-secondary text-uppercase" style={{ width: '200px', flexShrink: 0 }}>
                                Team Member
                            </div>
                            {days.map(day => (
                                <div key={day.toISOString()} className={clsx(
                                    "flex-grow-1 p-3 text-center border-end small fw-bold",
                                    isSameDay(day, new Date()) ? "bg-primary bg-opacity-10 text-primary" : "bg-white text-muted"
                                )}>
                                    {format(day, 'EEE d')}
                                </div>
                            ))}
                        </div>

                        {/* Rows */}
                        <div className="d-flex flex-column">
                            {users.map(user => {
                                const userTasks = tasks.filter(t => t.assigneeIds.includes(user.id));

                                return (
                                    <div key={user.id} className="d-flex border-bottom hover-bg-light transition-colors">
                                        <div className="p-3 border-end d-flex align-items-center gap-3" style={{ width: '200px', flexShrink: 0 }}>
                                            <img src={user.avatar} alt={user.name} className="rounded-circle" width="32" height="32" />
                                            <div className="min-w-0">
                                                <div className="small fw-bold text-dark text-truncate">{user.name}</div>
                                                <div className="text-muted text-truncate" style={{ fontSize: '10px' }}>{user.role}</div>
                                            </div>
                                        </div>

                                        {days.map(day => {
                                            const dayTasks = userTasks.filter(t => t.dueDate && isSameDay(new Date(t.dueDate), day));

                                            return (
                                                <div key={day.toISOString()} className="flex-grow-1 p-2 border-end" style={{ minHeight: '80px' }}>
                                                    <div className="d-flex flex-column gap-1">
                                                        {dayTasks.map(task => (
                                                            <div key={task.id} className={clsx(
                                                                "p-1 rounded text-truncate border",
                                                                task.status === 'Completed' ? "bg-success bg-opacity-10 text-success border-success-subtle" : "bg-white text-dark border-light"
                                                            )} style={{ fontSize: '10px' }}>
                                                                {task.title}
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
