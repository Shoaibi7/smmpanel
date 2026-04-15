@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full">
        <div class="text-[10px] font-bold text-secondary-500 dark:text-secondary-400 uppercase tracking-widest">
            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} results
        </div>

        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-end">
            <div class="inline-flex items-center rounded-xl bg-white dark:bg-secondary-800 border border-secondary-100 dark:border-secondary-700 shadow-sm overflow-hidden">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" class="px-3 py-2 text-secondary-300 dark:text-secondary-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 text-secondary-700 dark:text-secondary-200 hover:bg-secondary-50 dark:hover:bg-secondary-700/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif

                <div class="w-px h-8 bg-secondary-100 dark:bg-secondary-700"></div>

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="px-3 py-2 text-[10px] font-black text-secondary-400 dark:text-secondary-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="px-3 py-2 text-[10px] font-black bg-orange-600 text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-2 text-[10px] font-black text-secondary-700 dark:text-secondary-200 hover:bg-secondary-50 dark:hover:bg-secondary-700/50 transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <div class="w-px h-8 bg-secondary-100 dark:bg-secondary-700"></div>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 text-secondary-700 dark:text-secondary-200 hover:bg-secondary-50 dark:hover:bg-secondary-700/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span aria-disabled="true" class="px-3 py-2 text-secondary-300 dark:text-secondary-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </nav>
    </div>
@endif

