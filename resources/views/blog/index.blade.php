<x-marketing-layout>
    <section class="pt-32 pb-20 bg-secondary-50 dark:bg-secondary-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-4xl font-bold text-secondary-900 dark:text-white mb-4">Latest Insights</h1>
                <p class="text-secondary-600 dark:text-secondary-400">Tips, tricks, and official updates from SMM PRO.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <x-card class="flex flex-col h-full group transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 p-0 overflow-hidden">
                        <div class="aspect-video bg-secondary-200 dark:bg-secondary-800 relative overflow-hidden">
                            @if($post->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}" alt="{{ $post->title }}" class="object-cover w-full h-full">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-secondary-400 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-3 group-hover:text-primary-600 transition-colors">
                                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-sm text-secondary-600 dark:text-secondary-400 mb-6 flex-1">
                                {{ $post->excerpt }}
                            </p>
                            <div class="flex items-center justify-between mt-auto pt-6 border-t border-secondary-100 dark:border-secondary-800">
                                <span class="text-xs text-secondary-500 font-medium uppercase tracking-wider">{{ $post->created_at->format('M d, Y') }}</span>
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:underline">Read More →</a>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>

            @if($posts->hasPages())
                <div class="mt-12">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>
</x-marketing-layout>
