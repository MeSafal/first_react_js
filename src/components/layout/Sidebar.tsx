import React, { useState } from 'react';
import { NavLink, useLocation } from 'react-router-dom';
import {
    Briefcase,
    Calendar,
    ChevronDown,
    ChevronRight,
    Folder,
    Layout as LayoutIcon,
    Settings,
    Users,
    CheckSquare,
    FileText
} from 'lucide-react';
import { useBordioStore } from '../../store/useBordioStore';
import { clsx } from 'clsx'; // clsx is still useful for conditional classes, even with Bootstrap

const SidebarItem = ({ icon: Icon, label, to, onClick, active }: any) => {
    if (onClick) {
        return (
            <button
                onClick={onClick}
                className={clsx(
                    "w-100 d-flex align-items-center gap-2 px-3 py-2 border-0 bg-transparent transition-all",
                    active ? "text-primary bg-primary bg-opacity-10 border-end border-3 border-primary" : "text-white text-opacity-75 hover-bg-sidebar-light"
                )}
                style={{ textAlign: 'left', cursor: 'pointer' }}
            >
                <Icon size={18} />
                <span className="small font-weight-medium">{label}</span>
            </button>
        )
    }

    return (
        <NavLink
            to={to}
            className={({ isActive }) =>
                clsx('sidebar-item d-flex align-items-center gap-3 text-decoration-none', isActive && 'active')
            }
        >
            <Icon size={18} />
            <span className="small font-weight-medium">{label}</span>
        </NavLink>
    );
};

export const Sidebar = () => {
    const { projects, users } = useBordioStore();
    const [isTeamsOpen, setIsTeamsOpen] = useState(true);
    const currentUser = users[0];

    return (
        <div className="sidebar-fixed d-flex flex-column h-100 border-end border-dark">
            {/* Header */}
            <div className="p-3 mb-2">
                <div className="d-flex align-items-center gap-2 text-white cursor-pointer opacity-hover">
                    <div className="bg-primary rounded p-1">
                        <span className="fw-bold small">GA</span>
                    </div>
                    <div>
                        <div className="fw-bold small">GIM Agency</div>
                        <ChevronDown size={14} className="text-secondary" />
                    </div>
                </div>
            </div>

            {/* Main Navigation */}
            <div className="d-flex flex-column gap-1 mb-4">
                <SidebarItem to="/my-work" icon={CheckSquare} label="My Work" />
                <SidebarItem to="/calendar" icon={Calendar} label="Calendar" />
                <SidebarItem to="/kanban" icon={LayoutIcon} label="Kanban Board" />
                <SidebarItem to="/notes" icon={FileText} label="Notes" />
                <SidebarItem to="/team-workload" icon={Users} label="Team Workload" />
            </div>

            {/* Teams & Projects */}
            <div className="flex-grow-1 overflow-auto customs-scrollbar">
                <div
                    className="d-flex align-items-center justify-content-between px-3 py-2 text-white text-opacity-50 cursor-pointer hover-text-white"
                    onClick={() => setIsTeamsOpen(!isTeamsOpen)}
                >
                    <span className="text-uppercase small fw-bold" style={{ fontSize: '11px', letterSpacing: '0.5px' }}>Teams & Projects</span>
                    {isTeamsOpen ? <ChevronDown size={14} /> : <ChevronRight size={14} />}
                </div>

                {isTeamsOpen && (
                    <div className="mt-1 d-flex flex-column gap-1">
                        {/* Marketing Team Simulation */}
                        <div className="px-3 py-1 text-white text-opacity-75 small d-flex align-items-center gap-2">
                            <Folder size={14} className="text-secondary" /> Marketing
                        </div>
                        {projects.filter(p => ['p1', 'p2'].includes(p.id)).map(project => (
                            <SidebarItem
                                key={project.id}
                                to={`/projects/${project.id}`}
                                icon={Briefcase}
                                label={project.name}
                            />
                        ))}

                        {/* Design Team Simulation */}
                        <div className="px-3 py-1 mt-2 text-white text-opacity-75 small d-flex align-items-center gap-2">
                            <Folder size={14} className="text-secondary" /> Design
                        </div>
                        {projects.filter(p => !['p1', 'p2'].includes(p.id)).map(project => (
                            <SidebarItem
                                key={project.id}
                                to={`/projects/${project.id}`}
                                icon={Briefcase}
                                label={project.name}
                            />
                        ))}
                    </div>
                )}
            </div>

            {/* User Footer */}
            <div className="mt-auto p-3 border-top border-dark border-opacity-50">
                <div className="d-flex align-items-center gap-2">
                    <div className="position-relative">
                        <img src={currentUser.avatar} alt={currentUser.name} className="rounded-circle" width="32" height="32" />
                        <span className="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle" style={{ width: '8px', height: '8px' }}></span>
                    </div>
                    <div className="text-white text-opacity-90 small">
                        <div className="fw-medium">{currentUser.name}</div>
                        <Settings size={14} className="text-secondary cursor-pointer hover-text-white" />
                    </div>
                </div>
            </div>
        </div>
    );
};
