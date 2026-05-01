<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Access restricted</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-sans antialiased dark:bg-gray-900">
        <main class="flex min-h-screen items-center justify-center px-6">
            <section class="max-w-md rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-sm font-semibold uppercase text-indigo-600">Access restricted</p>
                <h1 class="mt-3 text-2xl font-semibold text-gray-900 dark:text-gray-100">You cannot perform that action.</h1>
                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Your account role does not have permission for that request.</p>
                <a href="{{ route('dashboard') }}" class="mt-6 inline-flex rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Go to dashboard</a>
            </section>
        </main>
    </body>
</html>
