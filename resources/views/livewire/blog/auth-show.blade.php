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

@php $layout = 'app'; @endphp

<div class="max-w-4xl space-y-6">
    <div>
        <a href="{{ route('blog.auth.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-bold text-primary-600 dark:text-primary-400 hover:underline">
            ← Back to Blog
        </a>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-secondary-900 dark:text-white leading-tight mt-3">
            {{ $post->title }}
        </h1>
        <div class="flex items-center gap-3 text-xs text-secondary-500 font-medium mt-2">
            <span>{{ $post->created_at->format('F d, Y') }}</span>
            <span>·</span>
            <span>Admin</span>
        </div>
    </div>

    @if($post->image)
        <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full h-auto">
        </div>
    @endif

    <article class="prose prose-sm lg:prose-base dark:prose-invert max-w-none text-secondary-700 dark:text-secondary-300">
        {!! $post->body !!}
    </article>
</div>
