<?php

use Livewire\Volt\Component;
use App\Models\Post;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'posts' => Post::where('is_published', true)->latest()->paginate(9),
        ];
    }
}; ?>

@php $layout = 'app'; @endphp

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-black text-secondary-900 dark:text-white uppercase tracking-tight">Blog</h2>
        <p class="text-[10px] text-secondary-500 font-bold uppercase tracking-widest mt-1">Tips, tricks, and official updates</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($posts as $post)
            <x-card class="flex flex-col h-full group transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 p-0 overflow-hidden">
                <div class="aspect-video bg-secondary-200 dark:bg-secondary-800 relative overflow-hidden">
                    @if($post->image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}" alt="{{ $post->title }}" class="object-cover w-full h-full">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-secondary-400 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="text-sm font-black text-secondary-900 dark:text-white mb-2 group-hover:text-primary-600 transition-colors leading-snug">
                        <a href="{{ route('blog.auth.show', $post->slug) }}" wire:navigate>{{ $post->title }}</a>
                    </h3>
                    <p class="text-xs text-secondary-500 dark:text-secondary-400 mb-4 flex-1 line-clamp-3">
                        {{ $post->excerpt }}
                    </p>
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-secondary-100 dark:border-secondary-800">
                        <span class="text-[10px] text-secondary-400 font-bold uppercase tracking-wider">{{ $post->created_at->format('M d, Y') }}</span>
                        <a href="{{ route('blog.auth.show', $post->slug) }}" wire:navigate class="text-xs font-black text-primary-600 dark:text-primary-400 hover:underline">Read More →</a>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="col-span-3 py-20 text-center text-secondary-400 text-sm">No posts yet.</div>
        @endforelse
    </div>

    @if($posts->hasPages())
        <div class="mt-6">{{ $posts->links() }}</div>
    @endif
</div>
