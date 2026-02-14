<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use App\Models\Note;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class BordioDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'Product Manager',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Sarah Miller',
                'email' => 'sarah@example.com',
                'role' => 'Designer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'role' => 'Developer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Emily Chen',
                'email' => 'emily@example.com',
                'role' => 'Marketing Lead',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'role' => 'Sales Manager',
                'password' => Hash::make('password'),
            ],
        ];

        $createdUsers = collect();
        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->avatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=' . substr(md5($user->name), 0, 6) . '&color=fff';
            $user->save();
            $createdUsers->push($user);
        }

        // Create Teams
        $teams = [
            ['name' => 'Marketing'],
            ['name' => 'Design'],
            ['name' => 'Development'],
            ['name' => 'Sales'],
        ];

        $createdTeams = collect();
        foreach ($teams as $teamData) {
            $team = Team::create($teamData);
            
            // Assign random members to teams
            $teamMembers = $createdUsers->random(rand(2, 4))->pluck('id')->toArray();
            $team->members()->attach($teamMembers);
            
            $createdTeams->push($team);
        }

        // Create Projects
        $projects = [
            ['name' => 'New Website Redesign', 'team_id' => $createdTeams[1]->id],
            ['name' => 'Mobile App Development', 'team_id' => $createdTeams[2]->id],
            ['name' => 'CRM System Integration', 'team_id' => $createdTeams[2]->id],
            ['name' => 'Q1 Marketing Campaign', 'team_id' => $createdTeams[0]->id],
            ['name' => 'Customer Onboarding Flow', 'team_id' => $createdTeams[3]->id],
        ];

        $createdProjects = collect();
        foreach ($projects as $projectData) {
            $project = Project::create($projectData);
            
            // Assign team members to project
            $teamMembers = $project->team->members->pluck('id')->toArray();
            $project->members()->attach($teamMembers);
            
            $createdProjects->push($project);
        }

        // Create Tasks
        $taskStatuses = ['Todo', 'In Progress', 'Under Review', 'Completed', 'Scheduled'];
        $priorities = ['Low', 'Normal', 'High', 'Urgent'];
        
        $taskTemplates = [
            // Website Redesign Tasks
            [
                'title' => 'Design Homepage Mockups',
                'description' => 'Create high-fidelity mockups for the new homepage design',
                'project_id' => $createdProjects[0]->id,
                'status' => 'In Progress',
                'priority' => 'High',
                'due_date' => Carbon::now()->addDays(3),
                'time_estimate' => 480, // 8 hours
                'tags' => ['design', 'ui/ux', 'high-priority'],
                'subtasks' => ['Research competitors', 'Create wireframes', 'Design mockups', 'Get feedback'],
            ],
            [
                'title' => 'Implement Responsive Navigation',
                'description' => 'Build responsive navigation component with mobile menu',
                'project_id' => $createdProjects[0]->id,
                'status' => 'Todo',
                'priority' => 'Normal',
                'due_date' => Carbon::now()->addDays(5),
                'time_estimate' => 240,
                'tags' => ['development', 'frontend'],
                'subtasks' => ['Desktop nav', 'Mobile hamburger menu', 'Test on devices'],
            ],
            [
                'title' => 'Setup Google Analytics',
                'description' => 'Configure GA4 tracking for the new website',
                'project_id' => $createdProjects[0]->id,
                'status' => 'Completed',
                'priority' => 'Normal',
                'due_date' => Carbon::now()->subDays(2),
                'time_estimate' => 60,
                'tags' => ['analytics', 'setup'],
            ],
            
            // Mobile App Tasks
            [
                'title' => 'User Authentication Flow',
                'description' => 'Implement login/signup with social auth',
                'project_id' => $createdProjects[1]->id,
                'status' => 'Under Review',
                'priority' => 'Urgent',
                'due_date' => Carbon::now()->addDays(1),
                'time_estimate' => 600,
                'tags' => ['mobile', 'security', 'auth'],
                'subtasks' => ['Email/password login', 'Google OAuth', 'Facebook OAuth', 'Password reset'],
            ],
            [
                'title' => 'Push Notifications Setup',
                'description' => 'Configure Firebase Cloud Messaging for push notifications',
                'project_id' => $createdProjects[1]->id,
                'status' => 'In Progress',
                'priority' => 'High',
                'due_date' => Carbon::now()->addDays(4),
                'time_estimate' => 360,
                'tags' => ['mobile', 'notifications'],
            ],
            
            // CRM Tasks
            [
                'title' => 'Database Schema Design',
                'description' => 'Design database schema for CRM integration',
                'project_id' => $createdProjects[2]->id,
                'status' => 'Completed',
                'priority' => 'High',
                'due_date' => Carbon::now()->subDays(5),
                'time_estimate' => 300,
                'tags' => ['database', 'architecture'],
            ],
            [
                'title' => 'API Integration Testing',
                'description' => 'Test all CRM API endpoints',
                'project_id' => $createdProjects[2]->id,
                'status' => 'Todo',
                'priority' => 'Normal',
                'due_date' => Carbon::now()->addDays(7),
                'time_estimate' => 480,
                'tags' => ['testing', 'api'],
                'subtasks' => ['Test GET endpoints', 'Test POST endpoints', 'Test error handling'],
            ],
            
            // Marketing Tasks
            [
                'title' => 'Social Media Content Calendar',
                'description' => 'Create content calendar for Q1',
                'project_id' => $createdProjects[3]->id,
                'status' => 'In Progress',
                'priority' => 'High',
                'due_date' => Carbon::now()->addDays(2),
                'time_estimate' => 240,
                'tags' => ['marketing', 'social-media'],
            ],
            [
                'title' => 'Email Campaign Templates',
                'description' => 'Design email templates for newsletter',
                'project_id' => $createdProjects[3]->id,
                'status' => 'Scheduled',
                'priority' => 'Normal',
                'due_date' => Carbon::now()->addDays(10),
                'time_estimate' => 180,
                'tags' => ['marketing', 'email'],
            ],
            
            // Customer Onboarding Tasks
            [
                'title' => 'Create Onboarding Video',
                'description' => 'Record and edit customer onboarding tutorial video',
                'project_id' => $createdProjects[4]->id,
                'status' => 'Todo',
                'priority' => 'High',
                'due_date' => Carbon::now()->addDays(6),
                'time_estimate' => 420,
                'tags' => ['video', 'onboarding'],
                'subtasks' => ['Write script', 'Record video', 'Edit video', 'Add subtitles'],
            ],
            [
                'title' => 'Update Documentation',
                'description' => 'Update customer-facing documentation',
                'project_id' => $createdProjects[4]->id,
                'status' => 'Under Review',
                'priority' => 'Normal',
                'due_date' => Carbon::now()->addDays(8),
                'time_estimate' => 240,
                'tags' => ['documentation'],
            ],
            
            // Personal tasks (no project)
            [
                'title' => 'Team Meeting Notes',
                'description' => 'Compile and share notes from weekly team meeting',
                'project_id' => null,
                'status' => 'Todo',
                'priority' => 'Low',
                'due_date' => Carbon::now(),
                'time_estimate' => 30,
                'tags' => ['meeting', 'notes'],
            ],
            [
                'title' => 'Review Performance Metrics',
                'description' => 'Analyze team performance metrics for the month',
                'project_id' => null,
                'status' => 'In Progress',
                'priority' => 'Normal',
                'due_date' => Carbon::now()->addDays(1),
                'time_estimate' => 120,
                'tags' => ['review', 'metrics'],
            ],
        ];

        foreach ($taskTemplates as $taskData) {
            $subtasks = $taskData['subtasks'] ?? [];
            unset($taskData['subtasks']);
            
            $task = Task::create($taskData);
            
            // Assign random users from the project or all users
            $assigneePool = $task->project 
                ? $task->project->members 
                : $createdUsers;
            
            $assignees = $assigneePool->random(rand(1, min(2, $assigneePool->count())))->pluck('id')->toArray();
            $task->assignees()->attach($assignees);
            
            // Create subtasks
            foreach ($subtasks as $index => $subtaskTitle) {
                $task->subtasks()->create([
                    'title' => $subtaskTitle,
                    'completed' => $task->status === 'Completed' ? true : ($index < count($subtasks) / 2),
                ]);
            }
            
            // Add some chat messages randomly
            if (rand(0, 1)) {
                $task->chatMessages()->create([
                    'content' => 'Started working on this task. Will update progress soon!',
                    'user_id' => $assignees[0],
                ]);
                
                if (rand(0, 1)) {
                    $task->chatMessages()->create([
                        'content' => 'Great progress! Let me know if you need any help.',
                        'user_id' => $createdUsers->random()->id,
                    ]);
                }
            }
        }

        // Create Notes
        $noteTemplates = [
            [
                'title' => 'Design System Guidelines',
                'content' => '<h3>Color Palette</h3><p>Primary: #3b82f6<br>Secondary: #64748b<br>Success: #22c55e</p><h3>Typography</h3><p>Headings: Inter Bold<br>Body: Inter Regular</p>',
                'color' => 'bg-blue-50',
                'preview' => 'Brand colors and typography guidelines for the design system',
            ],
            [
                'title' => 'Meeting Notes - Jan 15',
                'content' => '<ul><li>Discussed Q1 goals</li><li>Assigned new projects</li><li>Review sprint velocity</li></ul>',
                'color' => 'bg-yellow-50',
                'preview' => 'Weekly team meeting notes and action items',
            ],
            [
                'title' => 'API Endpoints Reference',
                'content' => '<h3>Authentication</h3><p>POST /api/auth/login<br>POST /api/auth/register</p><h3>Users</h3><p>GET /api/users<br>GET /api/users/:id</p>',
                'color' => 'bg-green-50',
                'preview' => 'Quick reference for all API endpoints',
            ],
        ];

        foreach ($noteTemplates as $noteData) {
            Note::create(array_merge($noteData, [
                'user_id' => $createdUsers->random()->id,
            ]));
        }

        $this->command->info('✅ Bordio demo data seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info("   - {$createdUsers->count()} Users");
        $this->command->info("   - {$createdTeams->count()} Teams");
        $this->command->info("   - {$createdProjects->count()} Projects");
        $this->command->info("   - " . Task::count() . " Tasks");
        $this->command->info("   - " . Note::count() . " Notes");
        $this->command->info('');
        $this->command->info('🔐 Login credentials:');
        $this->command->info('   Email: john@example.com');
        $this->command->info('   Password: password');
    }
}
