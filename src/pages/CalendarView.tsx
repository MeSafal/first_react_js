import React, { useState } from 'react';
import {
    DndContext,
    DragOverlay,
    useDroppable,
    DragEndEvent,
    DragStartEvent,
    PointerSensor,
    useSensor,
    useSensors
} from '@dnd-kit/core';
import { WaitingList } from '../components/calendar/WaitingList';
import { useBordioStore } from '../store/useBordioStore';
import { format, startOfWeek, addDays, isSameDay } from 'date-fns';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { clsx } from 'clsx';
import { Task } from '../types';

const CalendarDay = ({ date, tasks }: { date: Date, tasks: Task[] }) => {
    const { setNodeRef, isOver } = useDroppable({
        id: date.toISOString(),
        data: { date },
    });

    const totalMinutes = tasks.reduce((acc, t) => acc + t.timeEstimate, 0);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    const timeString = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;

    const isToday = isSameDay(date, new Date());

    return (
        <div
            ref={setNodeRef}
            className={clsx(
                "col border-end border-bottom p-2 transition-colors",
                isOver ? "bg-primary bg-opacity-10" : "bg-white",
                isToday && "bg-light"
            )}
            style={{ minHeight: '200px' }}
        >
            <div className={clsx("small fw-medium mb-2 d-flex justify-content-between align-items-center", isToday ? "text-primary" : "text-muted")}>
                <span>{format(date, 'EEE d')}</span>
                {tasks.length > 0 && <span className="badge bg-light text-secondary border">{timeString}</span>}
            </div>

            <div className="d-flex flex-column gap-2">
                {tasks.map(task => (
                    <div key={task.id} className="bg-white border p-2 rounded shadow-sm hover-border-primary cursor-pointer transition-all">
                        <div className="fw-medium text-dark text-truncate small">{task.title}</div>
                        <div className="d-flex align-items-center gap-1 mt-1">
                            <div className={clsx("rounded-circle", task.status === 'Completed' ? "bg-success" : "bg-primary")} style={{ width: '6px', height: '6px' }} />
                            <span className="text-muted" style={{ fontSize: '10px' }}>{task.timeEstimate}m</span>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export const CalendarView = () => {
    const { tasks, updateTask } = useBordioStore();
    const [currentDate, setCurrentDate] = useState(new Date());
    const [activeDragItem, setActiveDragItem] = useState<Task | null>(null);

    const startOfCurrentWeek = startOfWeek(currentDate, { weekStartsOn: 1 });
    const weekDays = Array.from({ length: 7 }).map((_, i) => addDays(startOfCurrentWeek, i));

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: {
                distance: 8,
            },
        })
    );

    const handleDragStart = (event: DragStartEvent) => {
        if (event.active.data.current?.task) {
            setActiveDragItem(event.active.data.current.task);
        }
    };

    const handleDragEnd = (event: DragEndEvent) => {
        const { active, over } = event;

        if (over && active.data.current?.task) {
            const date = new Date(over.id); // Droppable ID is ISO string
            const task = active.data.current.task as Task;

            // Check if it's a valid date drop
            if (!isNaN(date.getTime())) {
                updateTask(task.id, {
                    dueDate: date.toISOString(),
                    status: task.status === 'Todo' ? 'Scheduled' : task.status
                });
            }
        }
        setActiveDragItem(null);
    };

    return (
        <DndContext sensors={sensors} onDragStart={handleDragStart} onDragEnd={handleDragEnd}>
            <div className="d-flex h-100 align-items-stretch">
                {/* Main Calendar Content */}
                <div className="flex-grow-1 d-flex flex-column h-100 bg-white">
                    {/* Toolbar */}
                    <div className="p-3 border-bottom d-flex align-items-center justify-content-between">
                        <div className="d-flex align-items-center gap-3">
                            <h1 className="h5 fw-bold text-dark mb-0">{format(currentDate, 'MMMM yyyy')}</h1>
                            <div className="btn-group">
                                <button onClick={() => setCurrentDate(addDays(currentDate, -7))} className="btn btn-light btn-sm"><ChevronLeft size={16} /></button>
                                <button onClick={() => setCurrentDate(addDays(currentDate, 7))} className="btn btn-light btn-sm"><ChevronRight size={16} /></button>
                            </div>
                        </div>
                        <div className="small text-muted">
                            Week View
                        </div>
                    </div>

                    {/* Grid Header */}
                    <div className="d-flex border-bottom">
                        {weekDays.map(day => (
                            <div key={day.toISOString()} className="col py-2 tiny fw-bold text-muted text-uppercase text-center border-end">
                                {format(day, 'EEE')}
                            </div>
                        ))}
                    </div>

                    {/* Grid Body */}
                    <div className="flex-grow-1 overflow-auto">
                        <div className="d-flex h-100">
                            {weekDays.map(day => {
                                const dayTasks = tasks.filter(t => t.dueDate && isSameDay(new Date(t.dueDate), day));
                                return <CalendarDay key={day.toISOString()} date={day} tasks={dayTasks} />;
                            })}
                        </div>
                    </div>
                </div>

                {/* Sidebar */}
                <WaitingList />
            </div>

            <DragOverlay>
                {activeDragItem ? (
                    <div className="card shadow-lg border-primary" style={{ width: '200px', transform: 'rotate(2deg)' }}>
                        <div className="card-body p-2">
                            <div className="fw-medium text-dark small">{activeDragItem.title}</div>
                            <div className="small text-muted mt-1">{activeDragItem.timeEstimate}m</div>
                        </div>
                    </div>
                ) : null}
            </DragOverlay>
        </DndContext>
    );
};
