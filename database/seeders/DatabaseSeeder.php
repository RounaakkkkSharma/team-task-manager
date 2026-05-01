<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Support\RoleResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => RoleResolver::ADMIN_EMAIL,
            'role' => RoleResolver::roleForEmail(RoleResolver::ADMIN_EMAIL),
            'password' => Hash::make('password'),
        ]);

        $member = User::create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'role' => RoleResolver::roleForEmail('member@example.com'),
            'password' => Hash::make('password'),
        ]);

        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Website Launch',
            'description' => 'Plan, assign, and track the launch checklist for the client website.',
            'deadline' => now()->addDays(10)->toDateString(),
        ]);

        $project->members()->sync([$admin->id, $member->id]);

        Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'created_by' => $admin->id,
            'title' => 'Prepare landing page copy',
            'description' => 'Draft final copy for the public launch page.',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addDays(2)->toDateString(),
        ]);

        Task::create([
            'project_id' => $project->id,
            'assigned_to' => $admin->id,
            'created_by' => $admin->id,
            'title' => 'Configure production database',
            'description' => 'Add Railway database variables and run migrations.',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addDays(4)->toDateString(),
        ]);
    }
}
