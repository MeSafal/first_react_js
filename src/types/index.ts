export type User = {
  id: string;
  name: string;
  avatar: string;
  role: string;
};

export type Project = {
  id: string;
  name: string;
  folderId: string; // Maps to a Team or Folder
  members: string[]; // User IDs
};

export type TaskStatus = 'Todo' | 'Scheduled' | 'In Progress' | 'Under Review' | 'Completed';
export type TaskPriority = 'Urgent' | 'High' | 'Normal' | 'Low';

export type Subtask = {
  id: string;
  title: string;
  completed: boolean;
};

export type ChatMessage = {
  id: string;
  userId: string;
  content: string;
  timestamp: string; // ISO string
};

export type Task = {
  id: string;
  projectId: string; // "waiting-list" if not assigned to a project? No, waiting list is just no due date.
  title: string;
  status: TaskStatus;
  dueDate: string | null; // ISO string or null for Waiting List
  timeEstimate: number; // in minutes
  subtasks: Subtask[];
  chatHistory: ChatMessage[]; // Also serves as "comments"
  files: string[];
  assigneeIds: string[];
  tags: string[];
  priority: TaskPriority;
  description?: string;
  recurrence?: 'none' | 'daily' | 'weekly' | 'monthly';
};

export type Note = {
  id: string;
  title: string;
  content: string;
  colorTags: string[];
  updatedAt: string;
};

export type Team = {
  id: string;
  name: string;
  members: string[]; // User IDs
};

export type Folder = {
  id: string;
  name: string;
  teamId?: string;
};
