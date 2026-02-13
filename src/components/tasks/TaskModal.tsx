import React, { useState, useEffect, useRef } from 'react';
import { useBordioStore } from '../../store/useBordioStore';
import { X, Calendar, Clock, CheckSquare, MessageSquare, Send, Paperclip, ChevronRight, User as UserIcon, Trash2, Repeat } from 'lucide-react';
import { motion } from 'framer-motion';
import { format } from 'date-fns';
import { clsx } from 'clsx';

export const TaskModal = () => {
    const { activeTaskId, tasks, users, projects, closeTask, updateTask, addChatMessage } = useBordioStore();
    const task = tasks.find(t => t.id === activeTaskId);
    const project = projects.find(p => p.id === task?.projectId);

    // Chat State
    const [message, setMessage] = useState('');
    const [newSubtaskTitle, setNewSubtaskTitle] = useState('');
    const chatEndRef = useRef<HTMLDivElement>(null);

    // Scroll chat to bottom
    useEffect(() => {
        if (chatEndRef.current) {
            chatEndRef.current.scrollIntoView({ behavior: 'smooth' });
        }
    }, [task?.chatHistory]);

    if (!task) return null;

    const handleSendMessage = (e: React.FormEvent) => {
        e.preventDefault();
        if (!message.trim()) return;

        addChatMessage(task.id, {
            userId: 'u1', // Current user
            content: message
        });
        setMessage('');
    };

    const handleAddSubtask = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' && newSubtaskTitle.trim()) {
            const newSubtask = {
                id: `subtask-${Date.now()}`,
                title: newSubtaskTitle,
                completed: false
            };
            updateTask(task.id, { subtasks: [...task.subtasks, newSubtask] });
            setNewSubtaskTitle('');
        }
    };

    const toggleSubtask = (subtaskId: string, currentStatus: boolean) => {
        const updatedSubtasks = task.subtasks.map(s =>
            s.id === subtaskId ? { ...s, completed: !currentStatus } : s
        );
        updateTask(task.id, { subtasks: updatedSubtasks });
    };

    const completedSubtasks = task.subtasks.filter(s => s.completed).length;
    const progress = task.subtasks.length > 0 ? (completedSubtasks / task.subtasks.length) * 100 : 0;

    return (
        <>
            <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                onClick={closeTask}
                className="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"
                style={{ zIndex: 1040, backdropFilter: 'blur(4px)' }}
            />
            <motion.div
                initial={{ x: '100%' }}
                animate={{ x: 0 }}
                exit={{ x: '100%' }}
                transition={{ type: 'spring', stiffness: 300, damping: 30 }}
                className="position-fixed top-0 end-0 h-100 w-100 bg-white shadow-lg d-flex flex-column font-sans"
                style={{ zIndex: 1050, maxWidth: '800px' }}
            >
                {/* Header */}
                <div className="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div className="d-flex align-items-center gap-2 text-muted small">
                        <span>{project?.name || 'Project'}</span>
                        <ChevronRight size={14} />
                        <span className="text-truncate" style={{ maxWidth: '200px' }}>{task.title}</span>
                        <span className="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-2" style={{ fontSize: '10px' }}>NEW LOOK</span>
                    </div>
                    <div className="d-flex align-items-center gap-2">
                        <div className="text-muted small">Created {format(new Date(), 'MMM d')}</div>
                        <button onClick={closeTask} className="btn btn-light btn-sm rounded-circle p-2">
                            <X size={20} className="text-muted" />
                        </button>
                    </div>
                </div>

                <div className="d-flex flex-column flex-md-row flex-grow-1 overflow-hidden">
                    {/* Left Column: Details */}
                    <div className="flex-grow-1 overflow-auto p-4 border-end custom-scrollbar">
                        <div className="d-flex align-items-start justify-content-between mb-4">
                            <h2 className="h4 fw-bold text-dark">{task.title}</h2>
                        </div>

                        {/* Quick Actions */}
                        <div className="d-flex flex-wrap gap-2 mb-4">
                            <button className="btn btn-light border d-flex align-items-center gap-2 btn-sm text-muted">
                                <UserIcon size={14} /> Assignee
                            </button>
                            <button className="btn btn-light border d-flex align-items-center gap-2 btn-sm text-muted">
                                <Calendar size={14} /> Due Date
                            </button>
                            <button className="btn btn-light border d-flex align-items-center gap-2 btn-sm text-muted">
                                <Clock size={14} /> Estimation
                            </button>
                        </div>

                        {/* Fields Grid */}
                        <div className="row g-3 mb-4">
                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-muted text-uppercase">Status</label>
                                <select
                                    value={task.status}
                                    onChange={(e) => updateTask(task.id, { status: e.target.value as any })}
                                    className="form-select form-select-sm bg-light border-0 fw-medium"
                                >
                                    <option>Todo</option>
                                    <option>Scheduled</option>
                                    <option>In Progress</option>
                                    <option>Under Review</option>
                                    <option>Completed</option>
                                </select>
                            </div>
                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-muted text-uppercase">Priority</label>
                                <div className="d-flex align-items-center gap-2 p-2 bg-light rounded border border-transparent">
                                    <div className={clsx("rounded-circle", task.priority === 'Urgent' ? 'bg-danger' : 'bg-primary')} style={{ width: '12px', height: '12px' }} />
                                    <span className="small fw-medium text-dark">{task.priority}</span>
                                </div>
                            </div>
                        </div>

                        {/* Recurrence */}
                        <div className="mb-4">
                            <label className="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-2">
                                <Repeat size={14} /> Recurrence
                            </label>
                            <select
                                value={task.recurrence || 'none'}
                                onChange={(e) => updateTask(task.id, { recurrence: e.target.value as any })}
                                className="form-select form-select-sm bg-light border-0 fw-medium"
                            >
                                <option value="none">None</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                        {/* Subtasks */}
                        <div className="mb-4">
                            <div className="d-flex align-items-center justify-content-between mb-2">
                                <h3 className="h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                                    <CheckSquare size={16} /> Subtasks
                                </h3>
                                <span className="small text-muted">{completedSubtasks}/{task.subtasks.length}</span>
                            </div>

                            {/* Progress Bar */}
                            <div className="progress mb-3" style={{ height: '6px' }}>
                                <div className="progress-bar bg-primary transition-all" role="progressbar" style={{ width: `${progress}%` }} aria-valuenow={progress} aria-valuemin={0} aria-valuemax={100}></div>
                            </div>

                            <div className="d-flex flex-column gap-2">
                                {task.subtasks.map(subtask => (
                                    <div key={subtask.id} className="d-flex align-items-center gap-3 p-2 hover-bg-light rounded group border border-transparent hover-border-light">
                                        <input
                                            type="checkbox"
                                            checked={subtask.completed}
                                            onChange={() => toggleSubtask(subtask.id, subtask.completed)}
                                            className="form-check-input mt-0 cursor-pointer"
                                        />
                                        <span className={clsx("small flex-grow-1", subtask.completed && "text-muted text-decoration-line-through")}>{subtask.title}</span>
                                        <button
                                            onClick={(e) => {
                                                e.stopPropagation();
                                                const updatedSubtasks = task.subtasks.filter(s => s.id !== subtask.id);
                                                updateTask(task.id, { subtasks: updatedSubtasks });
                                            }}
                                            className="btn btn-sm btn-link text-muted p-0 opacity-50 hover-opacity-100"
                                            style={{ opacity: 0.5 }}
                                            onMouseEnter={(e) => e.currentTarget.style.opacity = '1'}
                                            onMouseLeave={(e) => e.currentTarget.style.opacity = '0.5'}
                                        >
                                            <Trash2 size={14} />
                                        </button>
                                    </div>
                                ))}
                                {task.subtasks.length === 0 && <div className="small text-muted fst-italic">No subtasks yet.</div>}

                                {/* Add Subtask Input */}
                                <div className="d-flex align-items-center gap-2 p-2 bg-light rounded mt-2">
                                    <CheckSquare size={14} className="text-muted" />
                                    <input
                                        type="text"
                                        value={newSubtaskTitle}
                                        onChange={(e) => setNewSubtaskTitle(e.target.value)}
                                        onKeyDown={handleAddSubtask}
                                        placeholder="Add subtask and press Enter..."
                                        className="form-control form-control-sm border-0 bg-transparent"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Chat */}
                    <div className="bg-light d-flex flex-column border-start" style={{ width: '320px', minWidth: '320px' }}>
                        <div className="p-3 border-bottom bg-white">
                            <h3 className="h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                                <MessageSquare size={16} /> Comments
                            </h3>
                        </div>

                        <div className="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-3 custom-scrollbar">
                            {task.chatHistory.map(msg => {
                                const user = users.find(u => u.id === msg.userId);
                                return (
                                    <div key={msg.id}>
                                        <div className="d-flex align-items-center gap-2 mb-1">
                                            <img src={user?.avatar} alt={user?.name} className="rounded-circle" width="24" height="24" />
                                            <span className="small fw-bold text-dark">{user?.name}</span>
                                            <span className="small text-muted" style={{ fontSize: '10px' }}>{format(new Date(msg.timestamp), 'h:mm a')}</span>
                                        </div>
                                        <div className="ms-4 small text-dark bg-white p-2 rounded shadow-sm border border-light">
                                            {msg.content}
                                        </div>
                                    </div>
                                );
                            })}
                            <div ref={chatEndRef} />
                        </div>

                        {/* Input Area */}
                        <div className="p-3 bg-white border-top">
                            <form onSubmit={handleSendMessage} className="position-relative">
                                <input
                                    type="text"
                                    placeholder="Write a comment..."
                                    className="form-control form-control-sm pe-5"
                                    value={message}
                                    onChange={(e) => setMessage(e.target.value)}
                                />
                                <button type="submit" className="btn btn-link btn-sm position-absolute top-50 end-0 translate-middle-y text-primary text-decoration-none">
                                    <Send size={16} />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </motion.div>
        </>
    );
};
