<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use App\Models\Note;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::firstOrCreate(['name' => 'manage tasks']);
        Permission::firstOrCreate(['name' => 'manage projects']);
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'manage teams']);
        Permission::firstOrCreate(['name' => 'manage notes']);

        // Create roles
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions(['manage tasks', 'manage notes']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(['manage tasks', 'manage projects', 'manage teams', 'manage notes']);

        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // ── Users ──────────────────────────────────────
        $superAdmin = User::firstOrCreate(['email' => 'subedigokul119@gmail.com'], [
            'name' => 'Gokul Subedi',
            'password' => Hash::make('password'),
            'avatar' => 'https://i.pravatar.cc/150?u=gokul',
            'role' => 'Super Admin',
        ]);
        $superAdmin->assignRole('super-admin');

        $alice = User::firstOrCreate(['email' => 'alice@bordio.com'], [
            'name' => 'Alice Smith',
            'password' => Hash::make('password'),
            'avatar' => 'https://i.pravatar.cc/150?u=alice',
            'role' => 'Designer',
        ]);
        $alice->assignRole('admin');

        $bob = User::firstOrCreate(['email' => 'bob@bordio.com'], [
            'name' => 'Bob Jones',
            'password' => Hash::make('password'),
            'avatar' => 'https://i.pravatar.cc/150?u=bob',
            'role' => 'Developer',
        ]);
        $bob->assignRole('admin');

        $charlie = User::firstOrCreate(['email' => 'charlie@bordio.com'], [
            'name' => 'Charlie Day',
            'password' => Hash::make('password'),
            'avatar' => 'https://i.pravatar.cc/150?u=charlie',
            'role' => 'Manager',
        ]);
        $charlie->assignRole('user');

        $dana = User::firstOrCreate(['email' => 'dana@bordio.com'], [
            'name' => 'Dana Lee',
            'password' => Hash::make('password'),
            'avatar' => 'https://i.pravatar.cc/150?u=dana',
            'role' => 'Sales',
        ]);
        $dana->assignRole('user');

        // ── Teams ──────────────────────────────────────
        $marketing = Team::firstOrCreate(['name' => 'Marketing']);
        $marketing->members()->syncWithoutDetaching([$superAdmin->id, $alice->id, $charlie->id]);

        $design = Team::firstOrCreate(['name' => 'Design']);
        $design->members()->syncWithoutDetaching([$alice->id, $bob->id, $superAdmin->id]);

        $sales = Team::firstOrCreate(['name' => 'Sales']);
        $sales->members()->syncWithoutDetaching([$charlie->id, $dana->id]);

        $development = Team::firstOrCreate(['name' => 'Development']);
        $development->members()->syncWithoutDetaching([$bob->id, $superAdmin->id]);

        // ── Projects ──────────────────────────────────
        $p1 = Project::firstOrCreate(['name' => 'New Website'], ['team_id' => $design->id]);
        $p1->members()->syncWithoutDetaching([$alice->id, $bob->id, $superAdmin->id]);

        $p2 = Project::firstOrCreate(['name' => 'Mobile App'], ['team_id' => $development->id]);
        $p2->members()->syncWithoutDetaching([$bob->id, $superAdmin->id, $charlie->id]);

        $p3 = Project::firstOrCreate(['name' => 'CRM Sync'], ['team_id' => $sales->id]);
        $p3->members()->syncWithoutDetaching([$charlie->id, $dana->id]);

        $p4 = Project::firstOrCreate(['name' => 'Q4 Strategy'], ['team_id' => $marketing->id]);
        $p4->members()->syncWithoutDetaching([$alice->id, $charlie->id, $superAdmin->id]);

        $p5 = Project::firstOrCreate(['name' => 'Server Migration'], ['team_id' => $development->id]);
        $p5->members()->syncWithoutDetaching([$bob->id, $superAdmin->id]);

        // ── Tasks ──────────────────────────────────────
        $tasks = [
            ['title' => 'Design Homepage', 'project_id' => $p1->id, 'status' => 'Completed', 'priority' => 'High', 'due_date' => now()->subDays(3), 'time_estimate' => 120, 'tags' => ['Design'], 'assignees' => [$alice->id, $superAdmin->id]],
            ['title' => 'Implement Hero Section', 'project_id' => $p1->id, 'status' => 'In Progress', 'priority' => 'Normal', 'due_date' => now()->addDay(), 'time_estimate' => 180, 'tags' => ['Dev'], 'assignees' => [$bob->id, $superAdmin->id],
             'subtasks' => [['title' => 'Slicing from Figma', 'completed' => true], ['title' => 'Make Responsive', 'completed' => false], ['title' => 'Add animations', 'completed' => false]]],
            ['title' => 'Setup React Native', 'project_id' => $p2->id, 'status' => 'Todo', 'priority' => 'Urgent', 'due_date' => now()->addDays(3), 'time_estimate' => 60, 'tags' => ['Setup'], 'assignees' => [$bob->id]],
            ['title' => 'Database Schema Design', 'project_id' => $p3->id, 'status' => 'Under Review', 'priority' => 'High', 'due_date' => now()->addDays(2), 'time_estimate' => 90, 'tags' => ['Backend'], 'assignees' => [$charlie->id]],
            ['title' => 'Footer Design', 'project_id' => $p1->id, 'status' => 'Todo', 'priority' => 'Low', 'due_date' => null, 'time_estimate' => 45, 'tags' => ['Design'], 'assignees' => [$alice->id]],
            ['title' => 'App Icon Design', 'project_id' => $p2->id, 'status' => 'Todo', 'priority' => 'Normal', 'due_date' => null, 'time_estimate' => 30, 'tags' => ['Design'], 'assignees' => [$alice->id, $superAdmin->id]],
            ['title' => 'Draft Q4 Goals', 'project_id' => $p4->id, 'status' => 'Scheduled', 'priority' => 'High', 'due_date' => now()->addDays(5), 'time_estimate' => 60, 'tags' => ['Planning'], 'assignees' => [$charlie->id, $superAdmin->id]],
            ['title' => 'Backup Production DB', 'project_id' => $p5->id, 'status' => 'Completed', 'priority' => 'Urgent', 'due_date' => now()->subDays(5), 'time_estimate' => 30, 'tags' => ['DevOps'], 'assignees' => [$bob->id]],
            ['title' => 'API Integration with Stripe', 'project_id' => $p3->id, 'status' => 'In Progress', 'priority' => 'High', 'due_date' => now()->addDays(1), 'time_estimate' => 240, 'tags' => ['Dev', 'API'], 'assignees' => [$bob->id, $dana->id]],
            ['title' => 'About Page Content', 'project_id' => $p1->id, 'status' => 'Todo', 'priority' => 'Low', 'due_date' => null, 'time_estimate' => 60, 'tags' => ['Content'], 'assignees' => [$alice->id, $superAdmin->id]],
            ['title' => 'Push Notifications Setup', 'project_id' => $p2->id, 'status' => 'Todo', 'priority' => 'Normal', 'due_date' => now()->addDays(7), 'time_estimate' => 120, 'tags' => ['Dev'], 'assignees' => [$bob->id]],
            ['title' => 'SEO Optimization', 'project_id' => $p1->id, 'status' => 'Scheduled', 'priority' => 'Normal', 'due_date' => now()->addDays(10), 'time_estimate' => 90, 'tags' => ['Marketing'], 'recurrence' => 'weekly', 'assignees' => [$alice->id, $superAdmin->id]],
            ['title' => 'Competitor Analysis Report', 'project_id' => $p4->id, 'status' => 'In Progress', 'priority' => 'Normal', 'due_date' => now(), 'time_estimate' => 150, 'tags' => ['Research'], 'assignees' => [$charlie->id, $superAdmin->id]],
            ['title' => 'Customer Onboarding Flow', 'project_id' => $p3->id, 'status' => 'Todo', 'priority' => 'High', 'due_date' => now()->addDays(4), 'time_estimate' => 180, 'tags' => ['UX', 'Sales'], 'assignees' => [$dana->id, $alice->id]],
            ['title' => 'Server Load Testing', 'project_id' => $p5->id, 'status' => 'Scheduled', 'priority' => 'High', 'due_date' => now()->addDays(2), 'time_estimate' => 120, 'tags' => ['DevOps'], 'assignees' => [$bob->id, $superAdmin->id],
             'subtasks' => [['title' => 'Setup JMeter', 'completed' => true], ['title' => 'Run stress tests', 'completed' => false], ['title' => 'Generate report', 'completed' => false]]],
        ];

        foreach ($tasks as $taskData) {
            $assignees = $taskData['assignees'] ?? [];
            $subtasksData = $taskData['subtasks'] ?? [];
            unset($taskData['assignees'], $taskData['subtasks']);

            $task = Task::firstOrCreate(['title' => $taskData['title']], $taskData);
            $task->assignees()->syncWithoutDetaching($assignees);

            foreach ($subtasksData as $st) {
                $task->subtasks()->firstOrCreate(['title' => $st['title']], $st);
            }
        }

        // ── Notes ──────────────────────────────────────
        Note::firstOrCreate(['title' => 'Q4 Marketing Strategy', 'user_id' => $superAdmin->id], [
            'content' => '<h3>Social Media Strategy</h3><p>Focus on Instagram Reels and TikTok for Q4. Budget allocation: 40% social, 35% paid search, 25% content marketing.</p><ul><li>Launch influencer campaign by Oct 15</li><li>New landing pages for Black Friday</li><li>Email nurture sequence for leads</li></ul>',
            'preview' => 'Focus on social media ads and influencer campaigns...',
            'color' => 'bg-primary',
        ]);

        Note::firstOrCreate(['title' => 'Sprint Planning Notes', 'user_id' => $superAdmin->id], [
            'content' => '<h3>Sprint 14 — Oct 28 to Nov 8</h3><p>Velocity target: 42 points. Key deliverables:</p><ul><li>Complete API v2 migration</li><li>Launch new dashboard UI</li><li>Fix critical auth bug (#1234)</li></ul><p><strong>Blockers:</strong> Waiting on design approval for mobile nav.</p>',
            'preview' => 'Sprint 14 velocity target and deliverables...',
            'color' => 'bg-success',
        ]);

        Note::firstOrCreate(['title' => 'Design System Ideas', 'user_id' => $superAdmin->id], [
            'content' => '<h3>Component Library Refresh</h3><p>Explore glassmorphism for card components. Consider:</p><ul><li>Frosted glass effect with backdrop-filter</li><li>Subtle gradients on CTAs</li><li>Micro-animations on hover states</li><li>Dark mode support from day one</li></ul>',
            'preview' => 'Explore glassmorphism and modern design patterns...',
            'color' => 'bg-info',
        ]);

        Note::firstOrCreate(['title' => 'Meeting Minutes — Client Review', 'user_id' => $superAdmin->id], [
            'content' => '<h3>Client: Acme Corp</h3><p>Date: Oct 25, 2023</p><p>Attendees: Alice, Bob, Client PM</p><ul><li>Client approved homepage mockup</li><li>Requested changes to color palette</li><li>Next milestone: Functional prototype by Nov 15</li></ul>',
            'preview' => 'Client approved homepage, requested color changes...',
            'color' => 'bg-warning',
        ]);

        // ── Chat Messages (seed a few for demo) ──
        $heroTask = Task::where('title', 'Implement Hero Section')->first();
        if ($heroTask) {
            $heroTask->chatMessages()->firstOrCreate(['content' => 'Started working on the hero section. The Figma file looks great!', 'user_id' => $bob->id]);
            $heroTask->chatMessages()->firstOrCreate(['content' => 'Make sure to use the new brand colors from the latest style guide.', 'user_id' => $alice->id]);
            $heroTask->chatMessages()->firstOrCreate(['content' => 'I added responsive breakpoints. Can you review?', 'user_id' => $bob->id]);
            $heroTask->chatMessages()->firstOrCreate(['content' => 'Looks good! Just fix the padding on mobile.', 'user_id' => $superAdmin->id]);
        }

        $apiTask = Task::where('title', 'API Integration with Stripe')->first();
        if ($apiTask) {
            $apiTask->chatMessages()->firstOrCreate(['content' => 'Setting up the Stripe SDK. Which API version should we target?', 'user_id' => $bob->id]);
            $apiTask->chatMessages()->firstOrCreate(['content' => 'Use the latest v2023-10. Also we need webhook handlers.', 'user_id' => $dana->id]);
        }
    }
}
