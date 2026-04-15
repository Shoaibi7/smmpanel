<?php

use Livewire\Volt\Component;
use App\Models\Post;
use Illuminate\Support\Str;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $title = '';
    public $content = '';
    public $excerpt = '';
    public $is_published = true;
    public $editingPostId = null;

    public function with()
    {
        return [
            'posts' => Post::latest()->paginate(10),
        ];
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:5',
            'content' => 'required',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'body' => $this->content,
            'excerpt' => $this->excerpt ?: Str::limit($this->content, 150),
            'is_published' => $this->is_published,
        ];

        if ($this->editingPostId) {
            Post::find($this->editingPostId)->update($data);
            $this->dispatch('toast', message: 'Post updated!', type: 'success');
        } else {
            Post::create($data);
            $this->dispatch('toast', message: 'Post created!', type: 'success');
        }

        $this->reset(['title', 'content', 'excerpt', 'is_published', 'editingPostId']);
    }

    public function edit($id)
    {
        $post = Post::find($id);
        $this->editingPostId = $post->id;
        $this->title = $post->title;
        $this->content = $post->body;
        $this->excerpt = $post->excerpt;
        $this->is_published = $post->is_published;
    }

    public function delete($id)
    {
        Post::find($id)->delete();
        $this->dispatch('toast', message: 'Post deleted!', type: 'info');
    }

    public function cancel()
    {
        $this->reset(['title', 'content', 'excerpt', 'is_published', 'editingPostId']);
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <x-card>
                    <x-slot name="header">
                        <h3 class="font-bold">{{ $editingPostId ? 'Edit Post' : 'Create New Post' }}</h3>
                    </x-slot>

                    <form wire:submit.prevent="save" class="space-y-4">
                        <div>
                            <x-input-label for="ptitle" value="Post Title" />
                            <x-text-input wire:model="title" id="ptitle" type="text" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <x-input-label for="pexcerpt" value="Excerpt (Short Summary)" />
                            <textarea wire:model="excerpt" id="pexcerpt" rows="2" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200"></textarea>
                        </div>

                        <div>
                            <x-input-label for="pcontent" value="Body Content" />
                            <textarea wire:model="content" id="pcontent" rows="6" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200"></textarea>
                        </div>

                        <div class="flex items-center space-x-2">
                            <input wire:model="is_published" type="checkbox" id="ppublished" class="rounded dark:bg-secondary-900 border-secondary-300 dark:border-secondary-700 text-primary-600 shadow-sm focus:ring-primary-500">
                            <x-input-label for="ppublished" value="Published" class="!mb-0" />
                        </div>

                        <div class="flex items-center space-x-2 pt-4">
                            <x-button type="submit" variant="primary" class="flex-1">{{ $editingPostId ? 'Update' : 'Create' }}</x-button>
                            @if($editingPostId)
                                <x-button type="button" variant="ghost" wire:click="cancel">Cancel</x-button>
                            @endif
                        </div>
                    </form>
                </x-card>
            </div>

            <div class="lg:col-span-2">
                <x-card class="p-0 overflow-hidden">
                    <x-table :headers="['Title', 'Status', 'Date', 'Actions']">
                        @foreach($posts as $post)
                            <tr class="hover:bg-secondary-50 dark:hover:bg-secondary-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-secondary-900 dark:text-white">{{ $post->title }}</div>
                                    <div class="text-[10px] text-secondary-500">/blog/{{ $post->slug }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-secondary-100 text-secondary-800' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-secondary-500">
                                    {{ $post->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <button wire:click="edit({{ $post->id }})" class="text-xs font-bold text-primary-600 hover:underline">Edit</button>
                                    <button wire:click="delete({{ $post->id }})" wire:confirm="Are you sure?" class="text-xs font-bold text-red-600 hover:underline">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                    <div class="p-4 bg-secondary-50 dark:bg-secondary-950">
                        {{ $posts->links() }}
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</div>
