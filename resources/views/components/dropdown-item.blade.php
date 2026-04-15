@props(['icon' => null])

<button {{ $attributes->merge(['type' => 'button', 'class' => 'w-full flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-secondary-700 dark:text-secondary-300 hover:bg-secondary-50 dark:hover:bg-secondary-700 transition-colors text-left']) }}>
    @if($icon)
        <span class="w-4 h-4 flex-shrink-0">
            {!! $icon !!}
        </span>
    @endif
    <span class="flex-1">{{ $slot }}</span>
</button>
