import React from 'react';
import { Sidebar } from './Sidebar';
import { Outlet } from 'react-router-dom';
import { useBordioStore } from '../../store/useBordioStore';
import { TaskModal } from '../tasks/TaskModal';
import { AnimatePresence } from 'framer-motion';

export const Layout = () => {
    const { activeTaskId } = useBordioStore();

    return (
        <div className="d-flex vh-100 bg-light overflow-hidden">
            <Sidebar />
            <main className="flex-grow-1 overflow-auto">
                {/* We could add a top header here if needed, but sidebar handles most navigation. 
            Pages will handle their own headers. */}
                <div className="flex-grow-1 overflow-auto p-3 p-md-4 custom-scrollbar">
                    <Outlet />
                </div>
                {/* Global Modal Overlay */}
                <AnimatePresence>
                    {activeTaskId && <TaskModal />}
                </AnimatePresence>
            </main>
        </div>
    );
};
