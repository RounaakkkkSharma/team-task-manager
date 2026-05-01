<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Team Task Manager</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Signed in as {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-flash-message :message="session('status')" />
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
                @foreach ([
                    'Projects' => $stats['projects'],
                    'Tasks' => $stats['tasks'],
                    'To Do' => $stats['todo'],
                    'In Progress' => $stats['in_progress'],
                    'Done' => $stats['done'],
                    'Overdue' => $stats['overdue'],
                ] as $label => $value)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <form method="GET" action="{{ route('dashboard') }}" class="grid gap-3 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-5">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="q">Search projects and tasks</label>
                        <input id="q" name="q" value="{{ $filters['q'] }}" placeholder="Search by project, task, assignee..." class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="status">Status</label>
                        <select id="status" name="status" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Any status</option>
                            @foreach (['todo' => 'To do', 'in_progress' => 'In progress', 'review' => 'Review', 'done' => 'Done'] as $value => $label)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="priority">Priority</label>
                        <select id="priority" name="priority" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Any priority</option>
                            @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                                <option value="{{ $value }}" @selected($filters['priority'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2 lg:col-span-3">
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Apply</button>
                        <a href="{{ route('dashboard') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Reset</a>
                    </div>
                </form>
            </section>

            <section x-data="{ createProjectOpen: @js($errors->any() && old('name')) }">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Projects <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $projects->count() }})</span></h3>
                    @if (auth()->user()->isAdmin())
                        <button type="button" x-on:click="createProjectOpen = true" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            New project
                        </button>
                    @endif
                </div>

                @if (auth()->user()->isAdmin())
                    <div
                        x-show="createProjectOpen"
                        x-on:keydown.escape.window="createProjectOpen = false"
                        class="fixed inset-0 z-40 overflow-y-auto px-4 py-6 sm:px-0"
                        style="display: none;"
                    >
                        <div class="fixed inset-0 bg-gray-900/75" x-on:click="createProjectOpen = false"></div>

                        <div x-show="createProjectOpen" x-transition class="relative mx-auto mt-10 max-w-3xl rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create Project</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add the project details and tick the team members who should belong to it.</p>
                                </div>
                                <button type="button" x-on:click="createProjectOpen = false" class="rounded-md px-2 py-1 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                    Close
                                </button>
                            </div>

                            <form method="POST" action="{{ route('projects.store') }}" class="mt-5 grid gap-4 md:grid-cols-2">
                                @csrf
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="name">Project name</label>
                                    <input id="name" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="deadline">Deadline</label>
                                    <input id="deadline" type="date" name="deadline" value="{{ old('deadline') }}" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="description">Description</label>
                                    <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('description') }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Team members</span>
                                    <div class="mt-1 grid max-h-48 gap-1 overflow-y-auto rounded-md border border-gray-300 bg-white p-2 dark:border-gray-700 dark:bg-gray-900 sm:grid-cols-2">
                                        @foreach ($users as $user)
                                            <label class="flex cursor-pointer items-center justify-between gap-3 rounded px-2 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                                                <span class="text-gray-800 dark:text-gray-100">{{ $user->name }} <span class="text-gray-500 dark:text-gray-400">- {{ $user->role }}</span></span>
                                                <input type="checkbox" name="members[]" value="{{ $user->id }}" @checked(collect(old('members', [auth()->id()]))->contains($user->id)) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900">
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 md:col-span-2">
                                    <button type="button" x-on:click="createProjectOpen = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                        Create project
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($projects as $project)
                        @php
                            $progress = $project->tasks_count ? round(($project->completed_tasks_count / $project->tasks_count) * 100) : 0;
                        @endphp
                        <a href="{{ route('projects.show', $project) }}" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:border-indigo-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $project->name }}</h4>
                                    <p class="mt-1 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">{{ $project->description ?: 'No description yet.' }}</p>
                                </div>
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $progress }}%</span>
                            </div>
                            <div class="mt-4 h-2 rounded-full bg-gray-100 dark:bg-gray-700">
                                <div class="h-2 rounded-full bg-indigo-600" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="mt-4 grid grid-cols-4 gap-2 text-center text-xs">
                                <span class="rounded bg-gray-100 px-2 py-1 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Todo {{ $project->todo_tasks_count }}</span>
                                <span class="rounded bg-sky-100 px-2 py-1 text-sky-700 dark:bg-sky-950 dark:text-sky-200">Doing {{ $project->in_progress_tasks_count }}</span>
                                <span class="rounded bg-violet-100 px-2 py-1 text-violet-700 dark:bg-violet-950 dark:text-violet-200">Review {{ $project->review_tasks_count }}</span>
                                <span class="rounded bg-red-100 px-2 py-1 text-red-700 dark:bg-red-950 dark:text-red-200">Late {{ $project->overdue_tasks_count }}</span>
                            </div>
                            <div class="mt-4 flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $project->tasks_count }} tasks</span>
                                <span>{{ $project->deadline ? $project->deadline->format('M d, Y') : 'No deadline' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 md:col-span-2 xl:col-span-3">
                            @if ($filters['q'])
                                No projects match your search.
                            @else
                                No projects yet. Admins can use New project to create the first one.
                            @endif
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ auth()->user()->isAdmin() ? 'All Tasks' : 'My Assigned Tasks' }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Task</th>
                                <th class="px-5 py-3">Project</th>
                                <th class="px-5 py-3">Assignee</th>
                                <th class="px-5 py-3">Priority</th>
                                <th class="px-5 py-3">Due</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($tasks as $task)
                                <tr>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</td>
                                    <td class="px-5 py-4"><a class="text-indigo-600 hover:underline" href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a></td>
                                    <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $task->assignee?->name ?: 'Unassigned' }}</td>
                                    <td class="px-5 py-4"><x-priority-badge :priority="$task->priority" /></td>
                                    <td class="px-5 py-4 {{ $task->is_overdue ? 'text-red-600' : 'text-gray-600 dark:text-gray-300' }}">{{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}</td>
                                    <td class="px-5 py-4">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                @foreach (['todo' => 'To do', 'in_progress' => 'In progress', 'review' => 'Review', 'done' => 'Done'] as $value => $label)
                                                    <option value="{{ $value }}" @selected($task->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                        @if ($filters['q'] || $filters['status'] || $filters['priority'])
                                            No tasks match the current search or filters.
                                        @else
                                            No tasks to show yet.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
