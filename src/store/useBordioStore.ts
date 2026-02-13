import { create } from 'zustand';
import { User, Project, Task, Note, Team, Folder, ChatMessage } from '../types';


// Dummy Data
const USERS: User[] = [
    { id: 'u1', name: 'Alice Smith', avatar: 'https://i.pravatar.cc/150?u=a', role: 'Designer' },
    { id: 'u2', name: 'Bob Jones', avatar: 'https://i.pravatar.cc/150?u=b', role: 'Developer' },
    { id: 'u3', name: 'Charlie Day', avatar: 'https://i.pravatar.cc/150?u=c', role: 'Manager' },
    { id: 'u4', name: 'Dana Lee', avatar: 'https://i.pravatar.cc/150?u=d', role: 'Sales' },
];

const TEAMS: Team[] = [
    { id: 't1', name: 'Marketing', members: ['u1', 'u3'] },
    { id: 't2', name: 'Design', members: ['u1', 'u2'] },
    { id: 't3', name: 'Sales', members: ['u3', 'u4'] },
];

const PROJECTS: Project[] = [
    { id: 'p1', name: 'New Website', folderId: 't2', members: ['u1', 'u2'] },
    { id: 'p2', name: 'Mobile App', folderId: 't2', members: ['u1', 'u2', 'u3'] },
    { id: 'p3', name: 'CRM Sync', folderId: 't3', members: ['u3', 'u4'] },
    { id: 'p4', name: 'Q4 Strategy', folderId: 't1', members: ['u1', 'u3'] },
    { id: 'p5', name: 'Server Migration', folderId: 't2', members: ['u2'] },
];

const INITIAL_TASKS: Task[] = [
    { id: 'task-1', projectId: 'p1', title: 'Design Homepage', status: 'Completed', dueDate: '2023-10-25T10:00:00Z', timeEstimate: 120, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u1'], tags: ['Design'], priority: 'High', recurrence: 'none' },
    { id: 'task-2', projectId: 'p1', title: 'Implement Hero Section', status: 'In Progress', dueDate: '2023-10-26T14:00:00Z', timeEstimate: 180, subtasks: [{ id: 's1', title: 'Slicing', completed: true }, { id: 's2', title: 'Responsive', completed: false }], chatHistory: [], files: [], assigneeIds: ['u2'], tags: ['Dev'], priority: 'Normal' },
    { id: 'task-3', projectId: 'p2', title: 'Setup React Native', status: 'Todo', dueDate: '2023-10-30T09:00:00Z', timeEstimate: 60, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u2'], tags: ['Setup'], priority: 'Urgent' },
    { id: 'task-4', projectId: 'p3', title: 'Database Schema', status: 'Under Review', dueDate: '2023-10-28T16:00:00Z', timeEstimate: 90, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u3'], tags: ['Backend'], priority: 'High' },
    { id: 'task-5', projectId: 'p1', title: 'Footer Design', status: 'Todo', dueDate: null, timeEstimate: 45, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u1'], tags: ['Design'], priority: 'Low' }, // Waiting list
    { id: 'task-6', projectId: 'p2', title: 'App Icon', status: 'Todo', dueDate: null, timeEstimate: 30, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u1'], tags: ['Design'], priority: 'Normal' }, // Waiting list
    { id: 'task-7', projectId: 'p4', title: 'Draft Q4 Goals', status: 'Scheduled', dueDate: '2023-11-01T10:00:00Z', timeEstimate: 60, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u3'], tags: ['Planning'], priority: 'High' },
    { id: 'task-8', projectId: 'p5', title: 'Backup DB', status: 'Completed', dueDate: '2023-10-20T00:00:00Z', timeEstimate: 30, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u2'], tags: ['DevOps'], priority: 'Urgent' },
    { id: 'task-9', projectId: 'p3', title: 'API Integration', status: 'In Progress', dueDate: '2023-10-29T11:00:00Z', timeEstimate: 240, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u2', 'u4'], tags: ['Dev'], priority: 'High' },
    { id: 'task-10', projectId: 'p1', title: 'About Page Content', status: 'Todo', dueDate: null, timeEstimate: 60, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u1'], tags: ['Content'], priority: 'Low' },
    { id: 'task-11', projectId: 'p2', title: 'Push Notifications', status: 'Todo', dueDate: '2023-11-05T10:00:00Z', timeEstimate: 120, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u2'], tags: ['Dev'], priority: 'Normal' },
    { id: 'task-12', projectId: 'p1', title: 'SEO Optimization', status: 'Scheduled', dueDate: '2023-11-10T10:00:00Z', timeEstimate: 90, subtasks: [], chatHistory: [], files: [], assigneeIds: ['u1'], tags: ['Marketing'], priority: 'Normal', recurrence: 'weekly' },
];

interface BordioState {
    users: User[];
    teams: Team[];
    projects: Project[];
    tasks: Task[];
    activeProjectId: string | null; // For filtering
    activeTaskId: string | null; // For Modal

    // Actions
    addTask: (task: Task) => void;
    updateTask: (id: string, updates: Partial<Task>) => void;
    deleteTask: (id: string) => void;
    duplicateTask: (id: string) => void;
    moveTask: (taskId: string, newStatus: Task['status']) => void; // For Kanban/List
    addTimeBlock: (taskId: string, minutes: number) => void;
    addChatMessage: (taskId: string, message: { userId: string, content: string }) => void;
    setActiveProject: (projectId: string | null) => void;
    openTask: (taskId: string) => void;
    closeTask: () => void;
    addProject: (project: Project) => void;
    deleteProject: (id: string) => void;
}

export const useBordioStore = create<BordioState>((set) => ({
    users: USERS,
    teams: TEAMS,
    projects: PROJECTS,
    tasks: INITIAL_TASKS,
    activeProjectId: null,
    activeTaskId: null,

    addTask: (task) => set((state) => ({ tasks: [...state.tasks, task] })),

    updateTask: (id, updates) => set((state) => ({
        tasks: state.tasks.map((t) => (t.id === id ? { ...t, ...updates } : t)),
    })),

    deleteTask: (id) => set((state) => ({
        tasks: state.tasks.filter((t) => t.id !== id),
    })),

    moveTask: (taskId, newStatus) => set((state) => ({
        tasks: state.tasks.map((t) => (t.id === taskId ? { ...t, status: newStatus } : t)),
    })),

    addTimeBlock: (taskId, minutes) => set((state) => ({
        tasks: state.tasks.map((t) => (t.id === taskId ? { ...t, timeEstimate: t.timeEstimate + minutes } : t)),
    })),

    addChatMessage: (taskId, message) => set((state) => ({
        tasks: state.tasks.map((t) => {
            if (t.id !== taskId) return t;
            const newMessage = {
                id: Math.random().toString(36).substr(2, 9),
                userId: message.userId,
                content: message.content,
                timestamp: new Date().toISOString(),
            };
            return { ...t, chatHistory: [...t.chatHistory, newMessage] };
        }),
    })),

    setActiveProject: (projectId) => set({ activeProjectId: projectId }),
    openTask: (taskId) => set({ activeTaskId: taskId }),
    closeTask: () => set({ activeTaskId: null }),

    duplicateTask: (id) => set((state) => {
        const taskToDupe = state.tasks.find(t => t.id === id);
        if (!taskToDupe) return state;
        const newTask = {
            ...taskToDupe,
            id: `task-${Date.now()}`,
            title: `${taskToDupe.title} (Copy)`,
            status: 'Todo' as const,
            chatHistory: []
        };
        return { tasks: [...state.tasks, newTask] };
    }),

    addProject: (project) => set((state) => ({ projects: [...state.projects, project] })),

    deleteProject: (id) => set((state) => ({
        projects: state.projects.filter(p => p.id !== id),
        tasks: state.tasks.filter(t => t.projectId !== id), // Also delete associated tasks
    })),
}));
