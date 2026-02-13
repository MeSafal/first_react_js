import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { Layout } from './components/layout/Layout';
import { ProjectView } from './pages/ProjectView';
import { CalendarView } from './pages/CalendarView';
import { KanbanBoard } from './pages/KanbanBoard';
import { NotesView } from './pages/NotesView';
import { TeamWorkloadView } from './pages/TeamWorkloadView';
import { MyWork } from './pages/MyWork';
import { AnimatePresence } from 'framer-motion';
import { TaskModal } from './components/tasks/TaskModal';
import { useBordioStore } from './store/useBordioStore';
import { ToastProvider } from './context/ToastContext';

function App() {
    const { activeTaskId } = useBordioStore();

    return (
        <ToastProvider>
            <BrowserRouter>
                <Routes>
                    <Route path="/" element={<Layout />}>
                        <Route index element={<Navigate to="/my-work" replace />} />
                        <Route path="my-work" element={<MyWork />} />
                        <Route path="projects/:id" element={<ProjectView />} />
                        <Route path="calendar" element={<CalendarView />} />
                        <Route path="kanban" element={<KanbanBoard />} />
                        <Route path="notes" element={<NotesView />} />
                        <Route path="team-workload" element={<TeamWorkloadView />} />
                    </Route>
                </Routes>
            </BrowserRouter>
            <AnimatePresence>{activeTaskId && <TaskModal />}</AnimatePresence>
        </ToastProvider>
    );
}

export default App;
