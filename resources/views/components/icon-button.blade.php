@props([
    'variant' => 'secondary',
    'size' => 'md',
    'type' => 'button'
])

@php
    $baseClasses = 'inline-flex items-center justify-center rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed active:scale-90';
    
    $variants = [
        'primary' => 'bg-orange-100 dark:bg-orange-900/20 text-orange-600 hover:bg-orange-600 hover:text-white focus:ring-orange-500',
        'secondary' => 'bg-secondary-100 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-300 hover:bg-secondary-900 dark:hover:bg-white hover:text-white dark:hover:text-black focus:ring-secondary-500',
        'danger' => 'bg-red-50 dark:bg-red-900/20 text-red-600 hover:bg-red-600 hover:text-white focus:ring-red-500',
        'ghost' => 'bg-transparent text-secondary-500 hover:bg-secondary-100 dark:hover:bg-secondary-800 focus:ring-secondary-500',
    ];

    $sizes = [
        'sm' => 'p-1.5 w-7 h-7',
        'md' => 'p-2 w-8 h-8',
        'lg' => 'p-2.5 w-10 h-10',
    ];

    $iconSizes = [
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
    ];

    $classes = "{$baseClasses} {$variants[$variant]} {$sizes[$size]}";
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    <span class="{{ $iconSizes[$size] }}">
        {{ $slot }}
    </span>
</button>
