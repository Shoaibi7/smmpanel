<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function with()
    {
        return [
            'users' => User::query()
                ->when($this->search, function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(15),
        ];
    }

    public function toggleRole($id)
    {
        $user = User::find($id);
        $user->update([
            'role' => $user->role === 'admin' ? 'user' : 'admin'
        ]);
        $this->dispatch('toast', message: 'User role updated!', type: 'success');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}; ?>

<div class="space-y-6 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-secondary-200 dark:border-secondary-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-secondary-900 dark:text-white tracking-tight">User Management</h2>
            <p class="text-[10px] uppercase font-bold text-secondary-400 tracking-wider mt-1">Manage system members and permissions</p>
        </div>
        
        <div class="bg-white dark:bg-secondary-900 rounded-xl shadow-sm border border-secondary-100 dark:border-secondary-800 p-1 flex items-center w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Search members..." 
                    class="w-full pl-9 pr-4 py-2 text-xs border-none rounded-lg bg-transparent text-secondary-900 dark:text-white font-medium focus:ring-0 placeholder-secondary-400 uppercase tracking-tight">
            </div>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($users as $user)
            <div class="bg-white dark:bg-secondary-900 rounded-2xl p-5 shadow-sm border border-secondary-100 dark:border-secondary-800 group hover:shadow-md hover:border-orange-200 dark:hover:border-orange-900/30 transition-all">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img class="w-12 h-12 rounded-xl object-cover border-2 border-secondary-50 dark:border-secondary-800" 
                                src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->role === 'admin' ? 'f97316' : '6366f1' }}&color=fff" alt="User">
                            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-secondary-900 {{ $user->role === 'admin' ? 'bg-orange-500' : 'bg-blue-500' }}"></div>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-secondary-900 dark:text-white uppercase tracking-tight">{{ $user->name }}</h4>
                            <p class="text-[10px] text-secondary-500 font-bold truncate max-w-[120px]">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="text-[8px] font-black text-secondary-400 uppercase tracking-widest">#{{ $user->id }}</span>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-secondary-50 dark:border-secondary-800/50">
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1">Role Status</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $user->role === 'admin' ? 'bg-orange-50 text-orange-600 dark:bg-orange-900/20' : 'bg-blue-50 text-blue-600 dark:bg-blue-900/20' }}">
                            {{ $user->role }}
                        </span>
                    </div>
                    
                    <button wire:click="toggleRole({{ $user->id }})" 
                        class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all {{ $user->role === 'admin' ? 'text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/20 dark:hover:bg-rose-900/40' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:hover:bg-emerald-900/40' }}">
                        {{ $user->role === 'admin' ? 'Revoke Admin' : 'Make Admin' }}
                    </button>
                </div>

                <div class="mt-4 flex items-center gap-2 text-[8px] font-black text-secondary-400 uppercase tracking-widest">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Joined {{ $user->created_at->format('M d, Y') }}
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center bg-white dark:bg-secondary-900 rounded-2xl border border-dashed border-secondary-200 dark:border-secondary-700">
                <svg class="w-12 h-12 text-secondary-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <p class="text-[10px] font-black text-secondary-400 uppercase tracking-widest">No members found matching your search</p>
            </div>
        @endforelse
    </div>

    <div class="pt-6">
        {{ $users->links() }}
    </div>
</div>

