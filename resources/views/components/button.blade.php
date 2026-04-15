@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'submit'
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed active:scale-95';
    
    $variants = [
        'primary' => 'bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-600 text-white border-transparent focus:ring-orange-500 shadow-lg shadow-orange-500/30',
        'secondary' => 'bg-secondary-100 dark:bg-secondary-700 text-secondary-700 dark:text-secondary-300 hover:bg-secondary-200 dark:hover:bg-secondary-600 border-transparent focus:ring-secondary-500',
        'outline' => 'bg-transparent border border-secondary-200 dark:border-secondary-700 text-secondary-700 dark:text-secondary-300 hover:bg-secondary-50 dark:hover:bg-secondary-800 focus:ring-orange-500',
        'danger' => 'bg-red-50 dark:bg-red-900/20 text-red-600 hover:bg-red-600 hover:text-white border-transparent focus:ring-red-500',
        'ghost' => 'bg-transparent text-secondary-600 dark:text-secondary-400 hover:bg-secondary-100 dark:hover:bg-secondary-800 focus:ring-orange-500',
    ];

    $sizes = [
        'xs' => 'px-2 py-1 text-[10px]',
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-xs',
        'lg' => 'px-5 py-2.5 text-sm',
        'xl' => 'px-6 py-3 text-sm',
    ];

    $classes = "{$baseClasses} {$variants[$variant]} {$sizes[$size]}";
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>
