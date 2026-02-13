import React, { useState } from 'react';
import { useParams } from 'react-router-dom';
import { useBordioStore } from '../store/useBordioStore';
import { TaskRow } from '../components/tasks/TaskRow';
import { ChevronDown, Plus, Search, Filter, MoreHorizontal, Check } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { ActionMenu } from '../components/common/ActionMenu';
import { useToast } from '../context/ToastContext';

export const ProjectView = () => {
    const { id } = useParams();
    const { projects, tasks, addTask, openTask, deleteTask, duplicateTask } = useBordioStore();
    const { showToast } = useToast();

    const project = projects.find(p => p.id === id);
    const projectTasks = tasks.filter(t => t.projectId === id);

    const activeTasks = projectTasks.filter(t => t.status !== 'Completed');
    const completedTasks = projectTasks.filter(t => t.status === 'Completed');

    const [newTaskTitle, setNewTaskTitle] = useState('');
    const [showCompleted, setShowCompleted] = useState(false);
    const [isAddingTask, setIsAddingTask] = useState(false);

    const handleTaskClick = (taskId: string) => {
        openTask(taskId);
    };

    const handleAddTask = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' && newTaskTitle.trim()) {
            const newTask = {
                id: `task - ${Date.now()} `,
                projectId: id!,
                title: newTaskTitle,
                status: 'Todo' as const,
                dueDate: null,
                timeEstimate: 30,
                subtasks: [],
                chatHistory: [],
                files: [],
                assigneeIds: [],
                tags: [],
                priority: 'Normal' as const,
            };
            addTask(newTask);
            setNewTaskTitle('');
            setIsAddingTask(false);
            showToast('Task created successfully', 'success');
        }
        if (e.key === 'Escape') {
            setNewTaskTitle('');
            setIsAddingTask(false);
        }
    };

    if (!project) return <div className="text-muted p-4">Project not found</div>;

    return (
        <div className="container-fluid py-4" style={{ maxWidth: '1000px' }}>
            {/* Header */}
            <header className="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <div className="small text-muted mb-1 text-uppercase fw-bold ls-1">Project / {project.folderId}</div>
                    <h1 className="h2 fw-bold text-dark mb-0">{project.name}</h1>
                </div>
                <div className="d-flex align-items-center gap-2">
                    <div className="d-flex align-items-center me-3">
                        {/* Project Members */}
                        <div className="rounded-circle bg-light border d-flex align-items-center justify-content-center small fw-bold text-secondary" style={{ width: '32px', height: '32px' }}>+3</div>
                    </div>
                    <button className="btn btn-light btn-sm d-flex align-items-center gap-2">
                        <Filter size={16} /> Filter
                    </button>
                    <button
                        onClick={() => setIsAddingTask(true)}
                        className="btn btn-primary btn-sm d-flex align-items-center gap-2"
                    >
                        <Plus size={16} /> Add Task
                    </button>
                </div>
            </header>

            {/* Active Tasks */}
            <section className="mb-5">
                <div className="d-flex align-items-center justify-content-between mb-3">
                    <h2 className="h5 fw-bold text-dark d-flex align-items-center gap-2 m-0">
                        Active Tasks
                        <span className="badge bg-secondary bg-opacity-10 text-secondary rounded-pill small">{activeTasks.length}</span>
                    </h2>
                </div>

                <div className="d-flex flex-column gap-2">
                    <AnimatePresence>
                        {activeTasks.map(task => (
                            <motion.div
                                key={task.id}
                                initial={{ opacity: 0, height: 0 }}
                                animate={{ opacity: 1, height: 'auto' }}
                                exit={{ opacity: 0, height: 0 }}
                                className="d-flex align-items-center gap-2"
                            >
                                <div className="flex-grow-1">
                                    <TaskRow task={task} onClick={() => handleTaskClick(task.id)} />
                                </div>
                                <ActionMenu
                                    onDuplicate={() => {
                                        duplicateTask(task.id);
                                        showToast('Task duplicated', 'success');
                                    }}
                                    onDelete={() => {
                                        deleteTask(task.id);
                                        showToast('Task deleted', 'success');
                                    }}
                                />
                            </motion.div>
                        ))}
                    </AnimatePresence>

                    {/* Inline Add Task Input */}
                    {isAddingTask && (
                        <div className="p-3 bg-light border rounded d-flex align-items-center gap-2">
                            <Check size={16} className="text-muted" />
                            <input
                                type="text"
                                value={newTaskTitle}
                                onChange={(e) => setNewTaskTitle(e.target.value)}
                                onKeyDown={handleAddTask}
                                placeholder="Type task name and press Enter..."
                                className="form-control form-control-sm border-0 bg-transparent"
                                autoFocus
                            />
                        </div>
                    )}
                </div>

                {/* Inline Add */}
                <div className="mt-2 d-flex align-items-center gap-3 p-3 text-muted border border-transparent rounded bg-white hover-shadow-sm cursor-text" onClick={() => document.getElementById('new-task-input')?.focus()}>
                    <Plus size={20} className="text-muted" />
                    <input
                        id="new-task-input"
                        type="text"
                        placeholder="Add a new task..."
                        className="form-control-plaintext border-0 shadow-none p-0 flex-grow-1 text-sm color-inherit"
                        value={newTaskTitle}
                        onChange={(e) => setNewTaskTitle(e.target.value)}
                        onKeyDown={handleAddTask}
                    />
                    <span className="badge bg-light text-muted border border-light-subtle">Enter</span>
                </div>
            </section>

            {/* Completed Tasks */}
            <section>
                <button
                    onClick={() => setShowCompleted(!showCompleted)}
                    className="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-2 mb-3 small fw-medium text-dark-hover"
                >
                    <ChevronDown size={16} className={`transition - transform duration - 200 ${showCompleted ? '' : '-rotate-90'} `} />
                    Completed Tasks ({completedTasks.length})
                </button>

                <AnimatePresence>
                    {showCompleted && (
                        <motion.div
                            initial={{ height: 0, opacity: 0 }}
                            animate={{ height: 'auto', opacity: 1 }}
                            exit={{ height: 0, opacity: 0 }}
                            className="overflow-hidden ps-3 border-start border-2 border-light d-flex flex-column gap-2"
                        >
                            {completedTasks.map(task => (
                                <TaskRow key={task.id} task={task} onClick={() => handleTaskClick(task.id)} />
                            ))}
                        </motion.div>
                    )}
                </AnimatePresence>
            </section>
        </div>
    );
};
