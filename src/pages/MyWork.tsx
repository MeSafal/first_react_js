import { useBordioStore } from '../store/useBordioStore';
import { TaskRow } from '../components/tasks/TaskRow';
import { format } from 'date-fns';
import { CheckCircle2, Clock, AlertCircle } from 'lucide-react';
import { AnimatePresence, motion } from 'framer-motion';

export const MyWork = () => {
    const { tasks, openTask, users } = useBordioStore();
    const currentUser = users[0]; // Alice - u1

    const myTasks = tasks.filter(t => t.assigneeIds.includes(currentUser.id) && t.status !== 'Completed');
    const completedTasks = tasks.filter(t => t.assigneeIds.includes(currentUser.id) && t.status === 'Completed');

    const today = new Date();
    const dueToday = myTasks.filter(t => t.dueDate && new Date(t.dueDate).getDate() === today.getDate());
    const overdue = myTasks.filter(t => t.dueDate && new Date(t.dueDate) < today && new Date(t.dueDate).getDate() !== today.getDate());
    const upcoming = myTasks.filter(t => !t.dueDate || new Date(t.dueDate) > today);

    return (
        <div className="container-fluid py-4" style={{ maxWidth: '1000px' }}>
            <header className="mb-5">
                <h1 className="h2 fw-bold text-dark">Good morning, {currentUser.name.split(' ')[0]}!</h1>
                <p className="text-muted mt-1">You have {myTasks.length} tasks to complete today. Let's get to work.</p>
            </header>

            {/* Stats */}
            <div className="row g-4 mb-5">
                <div className="col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body d-flex align-items-center gap-3">
                            <div className="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle" style={{ width: '48px', height: '48px' }}>
                                <AlertCircle size={24} />
                            </div>
                            <div>
                                <div className="h4 fw-bold mb-0 text-dark">{overdue.length}</div>
                                <div className="small fw-medium text-muted">Overdue</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body d-flex align-items-center gap-3">
                            <div className="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style={{ width: '48px', height: '48px' }}>
                                <Clock size={24} />
                            </div>
                            <div>
                                <div className="h4 fw-bold mb-0 text-dark">{dueToday.length}</div>
                                <div className="small fw-medium text-muted">Due Today</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body d-flex align-items-center gap-3">
                            <div className="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style={{ width: '48px', height: '48px' }}>
                                <CheckCircle2 size={24} />
                            </div>
                            <div>
                                <div className="h4 fw-bold mb-0 text-dark">{completedTasks.length}</div>
                                <div className="small fw-medium text-muted">Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="d-flex flex-column gap-5">
                {/* Overdue */}
                {overdue.length > 0 && (
                    <section>
                        <h2 className="h5 fw-bold text-danger mb-3 d-flex align-items-center gap-2">
                            Overdue <span className="badge bg-danger bg-opacity-10 text-danger rounded-pill small">{overdue.length}</span>
                        </h2>
                        <div className="d-flex flex-column gap-2">
                            {overdue.map(task => (
                                <TaskRow key={task.id} task={task} onClick={() => openTask(task.id)} />
                            ))}
                        </div>
                    </section>
                )}

                {/* Due Today */}
                <section>
                    <h2 className="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        Due Today <span className="badge bg-secondary bg-opacity-10 text-dark rounded-pill small">{dueToday.length}</span>
                    </h2>
                    <div className="d-flex flex-column gap-2">
                        {dueToday.map(task => (
                            <TaskRow key={task.id} task={task} onClick={() => openTask(task.id)} />
                        ))}
                        {dueToday.length === 0 && <div className="text-muted fst-italic small">No tasks due today. Great job!</div>}
                    </div>
                </section>

                {/* Upcoming */}
                <section>
                    <h2 className="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        Upcoming <span className="badge bg-secondary bg-opacity-10 text-dark rounded-pill small">{upcoming.length}</span>
                    </h2>
                    <div className="d-flex flex-column gap-2">
                        {upcoming.map(task => (
                            <TaskRow key={task.id} task={task} onClick={() => openTask(task.id)} />
                        ))}
                    </div>
                </section>
            </div>
        </div>
    );
};
