<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $project->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Owner: {{ $project->owner->name }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-indigo-600 hover:underline">Back to dashboard</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-flash-message :message="session('status')" />
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Project Details</h3>
                    @if (auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('projects.update', $project) }}" class="mt-4 grid gap-4 md:grid-cols-2">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="name">Name</label>
                                <input id="name" name="name" value="{{ old('name', $project->name) }}" required class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="deadline">Deadline</label>
                                <input id="deadline" type="date" name="deadline" value="{{ old('deadline', optional($project->deadline)->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="description">Description</label>
                                <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('description', $project->description) }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Team</span>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tick members, then save the project before assigning new tasks to them.</p>
                                <div class="mt-1 grid max-h-48 gap-1 overflow-y-auto rounded-md border border-gray-300 bg-white p-2 dark:border-gray-700 dark:bg-gray-900 sm:grid-cols-2">
                                    @foreach ($users as $user)
                                        <label class="flex cursor-pointer items-center justify-between gap-3 rounded px-2 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <span class="text-gray-800 dark:text-gray-100">{{ $user->name }} <span class="text-gray-500 dark:text-gray-400">- {{ $user->role }}</span></span>
                                            <input type="checkbox" name="members[]" value="{{ $user->id }}" @checked(collect(old('members', $project->members->pluck('id')->all()))->contains($user->id)) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900">
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex gap-3 md:col-span-2">
                                <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save project</button>
                            </div>
                        </form>
                    @else
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ $project->description ?: 'No description.' }}</p>
                    @endif
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Team</h3>
                    <div class="mt-4 space-y-3">
                        @foreach ($projectTeam as $member)
                            <div class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-900">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $member->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($member->role) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            @if (auth()->user()->isAdmin())
                <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Create Task</h3>
                    <form method="POST" action="{{ route('tasks.store', $project) }}" class="mt-4 grid gap-4 lg:grid-cols-6">
                        @csrf
                        <div class="lg:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="title">Title</label>
                            <input id="title" name="title" required class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="assigned_to">Assignee</label>
                            <select id="assigned_to" name="assigned_to" @disabled(! $hasTeammates) class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 disabled:cursor-not-allowed disabled:opacity-60">
                                <option value="">Unassigned</option>
                                @foreach ($projectTeam as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @if ($hasTeammates)
                                    Shows saved members of this project.
                                @else
                                    Add at least one team member and save the project before assigning tasks.
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="priority">Priority</label>
                            <select id="priority" name="priority" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="status">Status</label>
                            <select id="status" name="status" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="todo">To do</option>
                                <option value="in_progress">In progress</option>
                                <option value="review">Review</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="due_date">Due date</label>
                            <input id="due_date" type="date" name="due_date" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div class="lg:col-span-6">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="task_description">Description</label>
                            <textarea id="task_description" name="description" rows="2" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"></textarea>
                        </div>
                        <div class="lg:col-span-6">
                            <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create task</button>
                        </div>
                    </form>
                </section>
            @endif

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tasks</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($project->tasks as $task)
                        <div class="p-5">
                            <form method="POST" action="{{ route('tasks.update', $task) }}" class="grid gap-3 lg:grid-cols-12 lg:items-start">
                                @csrf
                                @method('PATCH')
                                <div class="lg:col-span-3">
                                    @if (auth()->user()->isAdmin())
                                        <input name="title" value="{{ $task->title }}" class="w-full rounded-md border-gray-300 text-sm font-semibold dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    @else
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $task->title }}</h4>
                                    @endif
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <x-priority-badge :priority="$task->priority" />
                                        @if ($task->is_overdue)
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-200 dark:bg-red-950 dark:text-red-200 dark:ring-red-800">Overdue</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $task->description }}</p>
                                </div>
                                @if (auth()->user()->isAdmin())
                                    <input type="hidden" name="description" value="{{ $task->description }}">
                                    <div class="lg:col-span-2">
                                        <select name="assigned_to" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                            <option value="">Unassigned</option>
                                            @foreach ($projectTeam as $member)
                                                <option value="{{ $member->id }}" @selected($task->assigned_to === $member->id)>{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="lg:col-span-2">
                                        <select name="priority" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                            @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                                                <option value="{{ $value }}" @selected($task->priority === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="lg:col-span-2">
                                        <input type="date" name="due_date" value="{{ optional($task->due_date)->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>
                                @else
                                    <div class="text-sm text-gray-600 dark:text-gray-300 lg:col-span-3">{{ $task->assignee?->name ?: 'Unassigned' }}</div>
                                    <div class="text-sm {{ $task->is_overdue ? 'text-red-600' : 'text-gray-600 dark:text-gray-300' }} lg:col-span-3">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</div>
                                @endif
                                <div class="lg:col-span-2">
                                    <select name="status" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @foreach (['todo' => 'To do', 'in_progress' => 'In progress', 'review' => 'Review', 'done' => 'Done'] as $value => $label)
                                            <option value="{{ $value }}" @selected($task->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 lg:col-span-1">
                                    <button class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Save</button>
                                </div>
                            </form>
                            @if (auth()->user()->isAdmin())
                                <button
                                    type="button"
                                    class="mt-2 text-sm text-red-600 hover:underline"
                                    x-data
                                    x-on:click="$dispatch('open-delete-modal', {
                                        action: @js(route('tasks.destroy', $task)),
                                        title: 'Delete task?',
                                        message: @js('This will permanently remove '.$task->title.' from this project.')
                                    })"
                                >
                                    Delete task
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            No tasks in this project yet. Admins can create the first task from the form above.
                        </div>
                    @endforelse
                </div>
            </section>

            @if (auth()->user()->isAdmin())
                <button
                    type="button"
                    class="text-sm font-medium text-red-600 hover:underline"
                    x-data
                    x-on:click="$dispatch('open-delete-modal', {
                        action: @js(route('projects.destroy', $project)),
                        title: 'Delete project?',
                        message: @js('This will permanently remove '.$project->name.' and all of its tasks.')
                    })"
                >
                    Delete project
                </button>
            @endif
        </div>
    </div>
</x-app-layout>
