<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskApiController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $memberIds = $project->members()->pluck('users.id')->push($project->owner_id)->unique();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer', Rule::in($memberIds->all())],
            'status' => ['required', Rule::in(['todo', 'in_progress', 'review', 'done'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date'],
        ]);

        $task = $project->tasks()->create($validated + [
            'created_by' => $request->user()->id,
        ]);

        return response()->json($task->load(['project', 'assignee']), 201);
    }

    public function update(Request $request, Task $task)
    {
        $user = $request->user();

        abort_unless($user->isAdmin() || (int) $task->assigned_to === (int) $user->id, 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['todo', 'in_progress', 'review', 'done'])],
        ]);

        $task->update($validated);

        return response()->json($task->fresh(['project', 'assignee']));
    }
}
