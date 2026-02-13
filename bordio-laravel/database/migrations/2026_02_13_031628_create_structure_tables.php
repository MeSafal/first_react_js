<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->string('role')->nullable(); // For display purposes (Developer, Designer, etc.)
        });

        // Teams (act as Folders in the UI)
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Team Members
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null'); // folderId
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Project Members
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('Todo'); // Todo, In Progress, Completed, etc.
            $table->string('priority')->default('Normal'); // Low, Normal, High, Urgent
            $table->dateTime('due_date')->nullable();
            $table->integer('time_estimate')->default(0); // in minutes
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade'); // Can be null for personal tasks
            $table->string('recurrence')->default('none'); // none, daily, weekly, monthly
            $table->json('tags')->nullable();
            $table->json('files')->nullable(); // Simple array of file URLs for now
            $table->timestamps();
        });

        // Subtasks
        Schema::create('subtasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('completed')->default(false);
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Task Assignees
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Chat Messages (Task Comments)
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Notes
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable(); // HTML or text content
            $table->string('preview')->nullable();
            $table->string('color')->default('bg-white');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Owner
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('task_user');
        Schema::dropIfExists('subtasks');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('project_user');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'role']);
        });
    }
};
