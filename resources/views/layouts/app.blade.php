<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <div
            x-data="{ open: false, action: '', title: '', message: '' }"
            x-on:open-delete-modal.window="
                action = $event.detail.action;
                title = $event.detail.title;
                message = $event.detail.message;
                open = true;
            "
            x-on:keydown.escape.window="open = false"
            x-show="open"
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
        >
            <div x-show="open" class="fixed inset-0 bg-gray-900/75" x-on:click="open = false"></div>

            <div x-show="open" x-transition class="relative mx-auto mt-24 max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="title"></h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300" x-text="message"></p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="open = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                        Cancel
                    </button>

                    <form method="POST" x-bind:action="action">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
