@props(['header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-secondary-900 border border-secondary-100 dark:border-secondary-800 rounded-xl shadow-sm overflow-hidden transition-colors duration-200']) }}>
    @if($header)
        <div class="px-6 py-4 border-b border-secondary-100 dark:border-secondary-800 bg-secondary-50/50 dark:bg-secondary-800/50">
            {{ $header }}
        </div>
    @endif

    <div class="px-6 py-4">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-4 border-t border-secondary-100 dark:border-secondary-800 bg-secondary-50/50 dark:bg-secondary-800/50">
            {{ $footer }}
        </div>
    @endif
</div>
