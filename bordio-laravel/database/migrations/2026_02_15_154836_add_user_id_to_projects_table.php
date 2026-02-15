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
        Schema::table('projects', function (Blueprint $row) {
            $row->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
        });

        // Set existing projects to be owned by the first admin or first user
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            \App\Models\Project::whereNull('user_id')->update(['user_id' => $firstUser->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $row) {
            $row->dropConstrainedForeignId('user_id');
        });
    }
};
