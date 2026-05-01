<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProjectApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['owner', 'members', 'tasks.assignee'])->latest();

        if (! $request->user()->isAdmin()) {
            $query->whereHas('members', fn ($members) => $members->where('users.id', $request->user()->id));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date'],
            'members' => ['array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $project = Project::create(Arr::except($validated, 'members') + [
            'owner_id' => $request->user()->id,
        ]);

        $project->members()->sync(collect($validated['members'] ?? [])->push($request->user()->id)->unique());

        return response()->json($project->load(['owner', 'members']), 201);
    }

    public function show(Request $request, Project $project)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $project->members()->where('users.id', $user->id)->exists(),
            403
        );

        return response()->json($project->load(['owner', 'members', 'tasks.assignee']));
    }
}
