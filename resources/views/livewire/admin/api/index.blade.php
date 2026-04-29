<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'users'    => User::where('role', '!=', 'admin')
                ->whereNotNull('api_token_key')
                ->latest()
                ->paginate(20),
            'endpoint' => \App\Models\Setting::where('key', 'api_endpoint')->value('value') ?? url('api/v1'),
        ];
    }

    public function regenerateToken(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['api_token_key' => \Illuminate\Support\Str::random(64)]);
        $this->dispatch('toast', message: 'Token regenerated for ' . $user->name, type: 'success');
    }
}; ?>

<div class="space-y-6 py-6">
    <!-- Header -->
    <div class="border-b border-secondary-200 dark:border-secondary-800 pb-4">
        <h2 class="text-lg font-black text-secondary-900 dark:text-white tracking-tight">API Management</h2>
        <p class="text-[10px] uppercase font-bold text-secondary-400 tracking-wider mt-0.5">Manage user API tokens and endpoints</p>
    </div>

    <!-- API Endpoint Info -->
    <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 p-6 shadow-sm">
        <h3 class="text-xs font-black text-secondary-900 dark:text-white uppercase tracking-widest mb-4">API Endpoint</h3>
        <div class="space-y-3">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">Base URL</label>
                <div class="flex items-center gap-2">
                    <code class="flex-1 px-4 py-2.5 bg-secondary-50 dark:bg-secondary-800 border border-secondary-200 dark:border-secondary-700 rounded-xl text-sm font-mono text-secondary-900 dark:text-white">
                        {{ $endpoint }}
                    </code>
                    <button onclick="navigator.clipboard.writeText('{{ $endpoint }}')"
                        class="px-3 py-2.5 bg-primary-600 hover:bg-primary-500 text-white text-[9px] font-black uppercase tracking-widest rounded-xl transition-all">
                        Copy
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                <div class="p-4 bg-secondary-50 dark:bg-secondary-800 rounded-xl border border-secondary-100 dark:border-secondary-700">
                    <p class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-1">Services Endpoint</p>
                    <code class="text-xs font-mono text-secondary-700 dark:text-secondary-300">GET {{ $endpoint }}/services?key=API_KEY</code>
                </div>
                <div class="p-4 bg-secondary-50 dark:bg-secondary-800 rounded-xl border border-secondary-100 dark:border-secondary-700">
                    <p class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-1">Authentication</p>
                    <code class="text-xs font-mono text-secondary-700 dark:text-secondary-300">?key=YOUR_API_TOKEN</code>
                </div>
            </div>
        </div>
    </div>

    <!-- Tokens Table -->
    <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-secondary-100 dark:border-secondary-800 flex items-center justify-between">
            <h3 class="text-xs font-black text-secondary-900 dark:text-white uppercase tracking-widest">User API Tokens</h3>
            <span class="text-[10px] font-bold text-secondary-400">{{ $users->total() }} users</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-secondary-50 dark:bg-secondary-900/80 border-b border-secondary-100 dark:border-secondary-800">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">User</th>
                        <th class="px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">API Token Key</th>
                        <th class="px-5 py-3 text-center text-[10px] font-black uppercase tracking-widest text-secondary-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-50 dark:divide-secondary-800/50">
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-secondary-50/50 dark:hover:bg-secondary-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=f97316&color=fff&size=32" class="w-8 h-8 rounded-lg" alt="">
                                    <div>
                                        <p class="text-xs font-black text-secondary-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-[10px] text-secondary-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <code class="text-[10px] font-mono text-secondary-600 dark:text-secondary-400 bg-secondary-50 dark:bg-secondary-800 px-2 py-1 rounded-lg max-w-[260px] truncate block">
                                        {{ $user->api_token_key }}
                                    </code>
                                    <button onclick="navigator.clipboard.writeText('{{ $user->api_token_key }}')"
                                        class="text-[9px] font-black text-primary-600 hover:text-primary-500 uppercase tracking-widest flex-shrink-0">
                                        Copy
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button wire:click="regenerateToken({{ $user->id }})"
                                    class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-blue-100 transition-all">
                                    Regenerate
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-[10px] font-black text-secondary-400 uppercase tracking-widest">
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-secondary-100 dark:border-secondary-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
