<?php

use Livewire\Volt\Component;
use App\Models\Post;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination, WithFileUploads;

    public $title = '';
    public $content = '';
    public $excerpt = '';
    public $is_published = true;
    public $editingPostId = null;
    public ?int $confirmingDeleteId = null;
    public $image = null;
    public $existingImage = null;

    public function with()
    {
        return [
            'posts' => Post::latest()->paginate(10),
        ];
    }

    public function save()
    {
        \Illuminate\Support\Facades\Log::info('Blog save() called', [
            'title'   => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'is_published' => $this->is_published,
        ]);

        $this->validate([
            'title' => 'required|min:3',
        ]);

        $body = trim(strip_tags($this->content));
        if ($body === '') {
            $this->addError('content', 'Body content is required.');
            \Illuminate\Support\Facades\Log::warning('Blog save() blocked: content is empty');
            return;
        }

        $imagePath = $this->existingImage;
        if ($this->image) {
            $imagePath = $this->image->store('blog', 'public');
        }

        $data = [
            'title'        => $this->title,
            'slug'         => Str::slug($this->title),
            'body'         => $this->content,
            'excerpt'      => $this->excerpt ?: Str::limit(strip_tags($this->content), 150),
            'is_published' => $this->is_published,
            'image'        => $imagePath,
        ];

        \Illuminate\Support\Facades\Log::info('Blog saving data', $data);

        if ($this->editingPostId) {
            Post::find($this->editingPostId)->update($data);
            $this->dispatch('toast', message: 'Post updated!', type: 'success');
        } else {
            Post::create($data);
            $this->dispatch('toast', message: 'Post created!', type: 'success');
        }

        $this->reset(['title', 'content', 'excerpt', 'is_published', 'editingPostId', 'image', 'existingImage']);
    }

    public function edit($id)
    {
        $post = Post::find($id);
        $this->editingPostId = $post->id;
        $this->title = $post->title;
        $this->content = $post->body;
        $this->excerpt = $post->excerpt;
        $this->is_published = $post->is_published;
        $this->existingImage = $post->image;
        $this->image = null;
        $this->dispatch('quill-set-content', content: $post->body);
    }

    public function delete($id)
    {
        Post::find($id)->delete();
        $this->confirmingDeleteId = null;
        $this->dispatch('toast', message: 'Post deleted!', type: 'info');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function cancel()
    {
        $this->reset(['title', 'content', 'excerpt', 'is_published', 'editingPostId', 'image', 'existingImage']);
    }
}; ?>

<div class="py-12" x-data="{ modalOpen: false }" @open-delete-modal.window="modalOpen = true">
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
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <x-input-label for="pexcerpt" value="Excerpt (Short Summary)" />
                            <textarea wire:model="excerpt" id="pexcerpt" rows="2" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200"></textarea>
                        </div>

                        <div>
                            <x-input-label value="Featured Image" />
                            <div class="mt-1">
                                @if($existingImage)
                                    <div class="mb-2 relative inline-block">
                                        <img src="{{ Storage::url($existingImage) }}" class="h-24 w-full object-cover rounded-lg border border-secondary-200 dark:border-secondary-700" alt="Current image">
                                        <button type="button" wire:click="$set('existingImage', null)" class="absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full text-xs flex items-center justify-center hover:bg-red-500">✕</button>
                                    </div>
                                @endif
                                <input wire:model="image" type="file" accept="image/*"
                                    class="block w-full text-xs text-secondary-600 dark:text-secondary-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-400 cursor-pointer">
                                @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <x-input-label for="pcontent" value="Body Content" />
                            <div class="mt-1" x-on:quill-updated.window="$wire.set('content', $event.detail.html)">
                                <div wire:ignore id="quill-editor-wrap">
                                    <div id="quill-editor" style="min-height:220px;"></div>
                                </div>
                            </div>
                            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                                    <button wire:click="confirmDelete({{ $post->id }})" class="text-xs font-bold text-red-600 hover:underline">Delete</button>
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

    <!-- Delete Confirmation Modal -->
    <div x-show="modalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-secondary-950/60 backdrop-blur-sm"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div @click.away="modalOpen = false"
            class="bg-white dark:bg-secondary-900 w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl border border-secondary-100 dark:border-secondary-800"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h3 class="text-lg font-black text-secondary-900 dark:text-white uppercase tracking-tight mb-2">Delete Post</h3>
                <p class="text-xs text-secondary-500 font-medium leading-relaxed mb-8">
                    Are you sure you want to delete this post? This action cannot be undone.
                </p>

                <div class="flex flex-col gap-3">
                    <button
                        @click="$wire.delete($wire.confirmingDeleteId).then(() => modalOpen = false)"
                        class="w-full py-4 text-white bg-red-600 hover:bg-red-500 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-red-600/20 transition-all active:scale-[0.98]">
                        Yes, Delete Post
                    </button>
                    <button @click="modalOpen = false"
                        class="text-[10px] font-black text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-200 uppercase tracking-widest transition-colors py-2">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var _quill = null;

        function bootQuill() {
            var el = document.getElementById('quill-editor');
            if (!el) return;
            if (typeof window.Quill === 'undefined') return;
            if (el.classList.contains('ql-container')) return;

            _quill = new window.Quill(el, {
                theme: 'snow',
                placeholder: 'Write your blog post content here...',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            // Sync directly to Livewire property on every keystroke
            _quill.on('text-change', function () {
                var html = _quill.root.innerHTML;
                if (html === '<p><br></p>') html = '';
                // Dispatch a browser event that Livewire listens to
                document.getElementById('quill-editor-wrap').dispatchEvent(
                    new CustomEvent('quill-updated', { bubbles: true, detail: { html: html } })
                );
            });

            // Populate when editing an existing post
            document.addEventListener('quill-set-content', function (e) {
                var html = e.detail?.content || (Array.isArray(e.detail) ? e.detail[0] : '') || '';
                if (_quill) _quill.root.innerHTML = html || '';
            });
        }

        setTimeout(bootQuill, 0);
        document.addEventListener('livewire:navigated', function () { _quill = null; setTimeout(bootQuill, 50); });
    })();
    </script>
</div>
