@props(['priority'])

@php
    $classes = [
        'high' => 'bg-red-100 text-red-700 ring-red-200 dark:bg-red-950 dark:text-red-200 dark:ring-red-800',
        'medium' => 'bg-amber-100 text-amber-700 ring-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:ring-amber-800',
        'low' => 'bg-emerald-100 text-emerald-700 ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:ring-emerald-800',
    ][$priority] ?? 'bg-gray-100 text-gray-700 ring-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset '.$classes]) }}>
    {{ ucfirst($priority) }}
</span>
