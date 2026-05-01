<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('status', 'Only admins can create projects.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date'],
            'members' => ['sometimes', 'array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $project = Project::create(Arr::except($validated, 'members') + [
            'owner_id' => $request->user()->id,
        ]);

        $project->members()->sync(collect($validated['members'] ?? [])->push($request->user()->id)->unique());

        return redirect()->route('projects.show', $project)->with('status', 'Project created.');
    }

    public function show(Request $request, Project $project)
    {
        if (! $this->canAccessProject($request, $project)) {
            return redirect()->route('dashboard')
                ->with('status', 'You do not belong to that project.');
        }

        $project->load(['owner', 'members', 'tasks.assignee']);
        $projectTeam = $project->members
            ->push($project->owner)
            ->unique('id')
            ->sortBy('name')
            ->values();

        return view('projects.show', [
            'project' => $project,
            'projectTeam' => $projectTeam,
            'hasTeammates' => $projectTeam->where('id', '!=', $project->owner_id)->isNotEmpty(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        if (! $request->user()->isAdmin()) {
            return redirect()->route('projects.show', $project)
                ->with('status', 'Only admins can update projects.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date'],
            'members' => ['sometimes', 'array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $project->update(Arr::except($validated, 'members'));
        $project->members()->sync(collect($validated['members'] ?? [])->push($project->owner_id)->unique());

        return back()->with('status', 'Project updated.');
    }

    public function destroy(Request $request, Project $project)
    {
        if (! $request->user()->isAdmin()) {
            return redirect()->route('projects.show', $project)
                ->with('status', 'Only admins can delete projects.');
        }

        $project->delete();

        return redirect()->route('dashboard')->with('status', 'Project deleted.');
    }

    private function canAccessProject(Request $request, Project $project): bool
    {
        $user = $request->user();

        return $user->isAdmin()
            || (int) $project->owner_id === (int) $user->id
            || $project->members()->where('users.id', $user->id)->exists();
    }
}
