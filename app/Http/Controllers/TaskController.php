<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->isAdmin() || (int) $task->assigned_to === (int) $user->id) {
            return redirect()->route('projects.show', $task->project)
                ->with('status', 'Task opened inside its project.');
        }

        return redirect()->route('dashboard')
            ->with('status', 'That task is not assigned to you.');
    }

    public function store(Request $request, Project $project)
    {
        if (! $request->user()->isAdmin()) {
            return back()->with('status', 'Only admins can create tasks.');
        }

        $memberIds = $project->members()->pluck('users.id')->push($project->owner_id)->unique();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer', Rule::in($memberIds->all())],
            'status' => ['required', Rule::in(['todo', 'in_progress', 'review', 'done'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date'],
        ]);

        $project->tasks()->create($validated + [
            'created_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Task created.');
    }

    public function update(Request $request, Task $task)
    {
        $user = $request->user();
        $isAssignee = (int) $task->assigned_to === (int) $user->id;

        if (! $user->isAdmin() && ! $isAssignee) {
            return redirect()->route('dashboard')
                ->with('status', 'You can only update tasks assigned to you.');
        }

        $rules = [
            'status' => ['required', Rule::in(['todo', 'in_progress', 'review', 'done'])],
        ];

        if ($user->isAdmin() && $request->has('title')) {
            $memberIds = $task->project->members()->pluck('users.id')->push($task->project->owner_id)->unique();
            $rules += [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:2000'],
                'assigned_to' => ['nullable', 'integer', Rule::in($memberIds->all())],
                'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
                'due_date' => ['nullable', 'date'],
            ];
        }

        $task->update($request->validate($rules));

        return back()->with('status', 'Task updated.');
    }

    public function destroy(Request $request, Task $task)
    {
        if (! $request->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('status', 'Only admins can delete tasks.');
        }

        $task->delete();

        return back()->with('status', 'Task deleted.');
    }
}
