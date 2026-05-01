<x-app-layout>
    <div class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100 py-12 transition-colors duration-300">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-10">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-slate-950 dark:text-white">
                        📝 Task Manager
                    </h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 max-w-2xl">
                        Keep the team in sync with clear tasks, quick status updates, and a fast delete action when work is complete.
                    </p>
                </div>

                <span class="inline-flex items-center rounded-full bg-slate-200/90 dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-4 py-2 text-sm font-medium shadow-sm">
                    {{ count($tasks) }} tasks
                </span>
            </div>

            <!-- Add Task -->
            <form method="POST" action="/tasks" class="grid gap-4 lg:grid-cols-[1fr_auto] mb-10">
                @csrf

                <div class="grid gap-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="title">Task title</label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        placeholder="Enter task title..."
                        required
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                    >

                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Optional description..."
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                >
                    ➕ Add Task
                </button>
            </form>

            <div class="border-t border-slate-200 dark:border-slate-800 mb-6"></div>

            <!-- Task List -->
            <div class="space-y-4">
                @forelse($tasks as $task)
                    <div class="group grid gap-4 md:grid-cols-[1fr_auto] items-center rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950 dark:text-white transition group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                {{ $task->title }}
                            </h2>

                            @if($task->description)
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $task->description }}
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <form method="POST" action="/tasks/{{ $task->id }}" class="flex">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100 hover:border-red-300 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200 dark:hover:bg-red-500/20"
                                >
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[28px] border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">
                        🚀 No tasks yet. Add a task to get the team moving.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
