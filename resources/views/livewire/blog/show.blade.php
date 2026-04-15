<?php

use Livewire\Volt\Component;
use App\Models\Post;

new class extends Component {
    public Post $post;

    public function mount($slug)
    {
        $this->post = Post::where('slug', $slug)->firstOrFail();
    }
}; ?>

<x-marketing-layout>
    <section class="pt-32 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <a href="/blog" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:underline mb-8 inline-block">&larr; Back to Blog</a>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-secondary-900 dark:text-white leading-tight mb-6">
                    {{ $post->title }}
                </h1>
                <div class="flex items-center space-x-4 text-sm text-secondary-500 font-medium">
                    <span>Published on {{ $post->created_at->format('F d, Y') }}</span>
                    <span>&bull;</span>
                    <span>Admin</span>
                </div>
            </div>

            @if($post->image)
                <div class="rounded-3xl overflow-hidden mb-12 shadow-2xl">
                    <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-auto">
                </div>
            @endif

            <article class="prose prose-lg dark:prose-invert max-w-none text-secondary-700 dark:text-secondary-300">
                {!! nl2br(e($post->body)) !!}
            </article>

            <div class="mt-16 pt-16 border-t border-secondary-100 dark:border-secondary-800">
                <div class="bg-secondary-50 dark:bg-secondary-900 rounded-3xl p-8 lg:p-12 text-center">
                    <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-4">Start Growing Your Audience Today</h3>
                    <p class="text-secondary-600 dark:text-secondary-400 mb-8 max-w-lg mx-auto">Join thousands of others using SMM PRO to dominate social media.</p>
                    <x-button variant="primary" size="lg" onclick="window.location.href='{{ route('register') }}'">Create Free Account</x-button>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
