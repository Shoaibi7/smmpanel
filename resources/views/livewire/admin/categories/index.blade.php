<?php

use Livewire\Volt\Component;
use App\Models\Category;
use Illuminate\Support\Str;

new class extends Component {
    public $categories;
    public $name = '';
    public $icon = '';
    public $is_active = true;
    public $editingCategoryId = null;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::orderBy('sort_order')->get();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'icon' => 'required',
        ]);

        if ($this->editingCategoryId) {
            $category = Category::find($this->editingCategoryId);
            $category->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', message: 'Category updated!', type: 'success');
        } else {
            Category::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'icon' => $this->icon,
                'is_active' => $this->is_active,
                'sort_order' => Category::max('sort_order') + 1,
            ]);
            $this->dispatch('toast', message: 'Category created!', type: 'success');
        }

        $this->reset(['name', 'icon', 'is_active', 'editingCategoryId']);
        $this->loadCategories();
    }

    public function edit($id)
    {
        $category = Category::find($id);
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->icon = $category->icon;
        $this->is_active = $category->is_active;
    }

    public function cancel()
    {
        $this->reset(['name', 'icon', 'is_active', 'editingCategoryId']);
    }

    public function delete($id)
    {
        Category::find($id)->delete();
        $this->loadCategories();
        $this->dispatch('toast', message: 'Category deleted!', type: 'info');
    }
}; ?>

<div class="space-y-6 py-6">
    <!-- Header -->
    <div class="border-b border-secondary-200 dark:border-secondary-800 pb-4">
        <h2 class="text-xl font-black text-secondary-900 dark:text-white tracking-tight">Category Management</h2>
        <p class="text-[10px] uppercase font-bold text-secondary-400 tracking-wider mt-1">Organize and manage service categories</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Form Side -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-secondary-100 dark:border-secondary-800 bg-secondary-50/50 dark:bg-secondary-900/50">
                    <h3 class="text-[10px] font-black text-secondary-900 dark:text-white uppercase tracking-widest">{{ $editingCategoryId ? 'Edit Category' : 'New Category' }}</h3>
                </div>
                
                <form wire:submit.prevent="save" class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-secondary-400 uppercase tracking-widest ml-1">Category Name</label>
                        <input wire:model="name" type="text" placeholder="e.g. Instagram" 
                            class="w-full px-4 py-3 text-xs font-bold border border-secondary-200 dark:border-secondary-700 rounded-xl bg-secondary-50 dark:bg-secondary-800 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-secondary-400 uppercase tracking-widest ml-1 text-xs">Icon (FontAwesome)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary-400">
                                <i class="{{ $icon ?: 'fas fa-icons' }}"></i>
                            </span>
                            <input wire:model="icon" type="text" placeholder="fab fa-instagram" 
                                class="w-full pl-11 pr-4 py-3 text-xs font-bold border border-secondary-200 dark:border-secondary-700 rounded-xl bg-secondary-50 dark:bg-secondary-800 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                        </div>
                        <x-input-error :messages="$errors->get('icon')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between p-3 bg-secondary-50 dark:bg-secondary-800 rounded-xl border border-secondary-100 dark:border-secondary-700">
                        <label class="text-[10px] font-black text-secondary-500 uppercase tracking-widest">Active Status</label>
                        <button type="button" 
                            wire:click="$toggle('is_active')"
                            class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $is_active ? 'bg-orange-500' : 'bg-secondary-300 dark:bg-secondary-600' }}">
                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <button type="submit" class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-orange-500/20 active:scale-95">
                            {{ $editingCategoryId ? 'Save Changes' : 'Create Category' }}
                        </button>
                        @if($editingCategoryId)
                            <button type="button" wire:click="cancel" class="w-full py-3 text-secondary-500 hover:text-secondary-700 dark:hover:text-secondary-300 text-[10px] font-black uppercase tracking-widest transition-all">
                                Cancel Edit
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Promotion Card -->
            <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl p-6 text-white shadow-lg shadow-orange-500/20 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform"></div>
                <h4 class="text-sm font-black uppercase tracking-tight relative mb-2">Pro Tip</h4>
                <p class="text-[10px] font-bold text-orange-50 relative opacity-90 leading-relaxed uppercase">Use FontAwesome 5 classes for icons to ensure they appear correctly on the user-side navigation bar.</p>
            </div>
        </div>

        <!-- List Side -->
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-secondary-100 dark:border-secondary-800 flex items-center justify-between">
                    <h3 class="text-[10px] font-black text-secondary-900 dark:text-white uppercase tracking-widest">Category List</h3>
                    <span class="text-[10px] font-black text-secondary-400 uppercase tracking-widest px-2 py-1 bg-secondary-50 dark:bg-secondary-800 rounded-lg">Total: {{ count($categories) }}</span>
                </div>

                <div class="divide-y divide-secondary-100 dark:divide-secondary-800">
                    @forelse($categories as $cat)
                        <div class="group flex items-center justify-between p-6 hover:bg-secondary-50/50 dark:hover:bg-secondary-800/30 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white dark:bg-secondary-800 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-700 flex items-center justify-center text-xl text-secondary-500 group-hover:text-orange-600 group-hover:scale-110 transition-all">
                                    <i class="{{ $cat->icon }}"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-secondary-900 dark:text-white uppercase tracking-tight">{{ $cat->name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-black uppercase {{ $cat->is_active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' }}">
                                            {{ $cat->is_active ? 'Active' : 'Hidden' }}
                                        </span>
                                        <span class="text-[8px] font-black text-secondary-400 uppercase tracking-widest">Order: {{ $cat->sort_order }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                <button wire:click="edit({{ $cat->id }})" class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button wire:click="delete({{ $cat->id }})" wire:confirm="Are you sure?" class="p-2 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-xl hover:bg-rose-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 flex flex-col items-center justify-center opacity-40">
                            <i class="fas fa-layer-group text-4xl mb-4"></i>
                            <p class="text-[10px] font-black uppercase tracking-widest">No categories created yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

