<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'status' => $request->query('status', ''),
            'priority' => $request->query('priority', ''),
        ];

        $projectsQuery = Project::query()
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'done'),
                'tasks as todo_tasks_count' => fn ($query) => $query->where('status', 'todo'),
                'tasks as in_progress_tasks_count' => fn ($query) => $query->where('status', 'in_progress'),
                'tasks as review_tasks_count' => fn ($query) => $query->where('status', 'review'),
                'tasks as overdue_tasks_count' => fn ($query) => $query
                    ->whereDate('due_date', '<', today())
                    ->where('status', '!=', 'done'),
            ])
            ->latest();

        if (! $user->isAdmin()) {
            $projectsQuery->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('members', fn ($memberQuery) => $memberQuery->where('users.id', $user->id));
            });
        }

        $accessibleProjectsCount = (clone $projectsQuery)->count();

        if ($filters['q'] !== '') {
            $projectsQuery->where(function ($query) use ($filters) {
                $query->where('name', 'like', '%'.$filters['q'].'%')
                    ->orWhere('description', 'like', '%'.$filters['q'].'%');
            });
        }

        $projects = $projectsQuery->get();

        $allTasksQuery = Task::with(['project', 'assignee'])->latest('due_date');

        if (! $user->isAdmin()) {
            $allTasksQuery->where('assigned_to', $user->id);
        }

        $allTasks = $allTasksQuery->get();

        $tasksQuery = Task::with(['project', 'assignee'])->latest('due_date');

        if (! $user->isAdmin()) {
            $tasksQuery->where('assigned_to', $user->id);
        }

        if ($filters['q'] !== '') {
            $tasksQuery->where(function ($query) use ($filters) {
                $query->where('title', 'like', '%'.$filters['q'].'%')
                    ->orWhere('description', 'like', '%'.$filters['q'].'%')
                    ->orWhereHas('project', fn ($projectQuery) => $projectQuery->where('name', 'like', '%'.$filters['q'].'%'))
                    ->orWhereHas('assignee', fn ($assigneeQuery) => $assigneeQuery->where('name', 'like', '%'.$filters['q'].'%'));
            });
        }

        if (in_array($filters['status'], ['todo', 'in_progress', 'review', 'done'], true)) {
            $tasksQuery->where('status', $filters['status']);
        }

        if (in_array($filters['priority'], ['low', 'medium', 'high'], true)) {
            $tasksQuery->where('priority', $filters['priority']);
        }

        $tasks = $tasksQuery->get();

        return view('dashboard', [
            'projects' => $projects,
            'tasks' => $tasks,
            'filters' => $filters,
            'users' => User::orderBy('name')->get(),
            'stats' => [
                'projects' => $accessibleProjectsCount,
                'tasks' => $allTasks->count(),
                'todo' => $allTasks->where('status', 'todo')->count(),
                'in_progress' => $allTasks->where('status', 'in_progress')->count(),
                'done' => $allTasks->where('status', 'done')->count(),
                'overdue' => $allTasks->filter->is_overdue->count(),
            ],
        ]);
    }
}
