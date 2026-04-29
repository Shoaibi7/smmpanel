<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    // Deduct balance modal
    public ?int $deductUserId   = null;
    public string $deductAmount = '';
    public string $deductNote   = '';

    // Block modal
    public ?int $blockUserId  = null;
    public string $blockAction = ''; // 'block' or 'unblock'

    public function with(): array
    {
        return [
            'users' => User::query()
                ->where('role', '!=', 'admin')
                ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%'))
                ->withCount('orders')
                ->latest()
                ->paginate(15),
        ];
    }

    // ── Add Funds ────────────────────────────────────────────────
    public ?int $addFundsUserId = null;
    public string $addFundsAmount = '';
    public string $addFundsNote = '';

    public function openAddFunds(int $id): void
    {
        $this->addFundsUserId = $id;
        $this->addFundsAmount = '';
        $this->addFundsNote = '';
        $this->dispatch('open-add-funds-modal');
    }

    public function processAddFunds(): void
    {
        $this->validate([
            'addFundsAmount' => 'required|numeric|min:0.01',
        ]);

        $user = User::findOrFail($this->addFundsUserId);
        $user->incrementBalance((float) $this->addFundsAmount);

        $this->dispatch('toast', message: 'Funds added successfully.', type: 'success');
        $this->dispatch('close-add-funds-modal');
        $this->reset(['addFundsUserId', 'addFundsAmount', 'addFundsNote']);
    }

    // ── Deduct Balance ──────────────────────────────────────────
    public function openDeduct(int $id): void
    {
        $this->deductUserId = $id;
        $this->deductAmount = '';
        $this->deductNote   = '';
        $this->dispatch('open-deduct-modal');
    }

    public function processDeduct(): void
    {
        $this->validate([
            'deductAmount' => 'required|numeric|min:0.01',
        ]);

        $user = User::findOrFail($this->deductUserId);

        if ((float) $this->deductAmount > (float) $user->balance) {
            $this->addError('deductAmount', 'Amount exceeds user balance of '.format_currency($user->balance));
            return;
        }

        $user->decrementBalance((float) $this->deductAmount);

        $this->dispatch('toast', message: 'Balance deducted successfully.', type: 'success');
        $this->dispatch('close-deduct-modal');
        $this->reset(['deductUserId', 'deductAmount', 'deductNote']);
    }

    // ── Block / Unblock ─────────────────────────────────────────
    public function openBlock(int $id, string $action): void
    {
        $this->blockUserId  = $id;
        $this->blockAction  = $action;
        $this->dispatch('open-block-modal');
    }

    public function processBlock(): void
    {
        $user = User::findOrFail($this->blockUserId);
        $user->update(['is_blocked' => $this->blockAction === 'block']);

        $msg = $this->blockAction === 'block' ? 'User blocked.' : 'User unblocked.';
        $this->dispatch('toast', message: $msg, type: $this->blockAction === 'block' ? 'warning' : 'success');
        $this->dispatch('close-block-modal');
        $this->reset(['blockUserId', 'blockAction']);
    }

    public function updatingSearch(): void { $this->resetPage(); }
}; ?>

<div
    x-data="{ deductOpen: false, blockOpen: false, addFundsOpen: false }"
    @open-deduct-modal.window="deductOpen = true"
    @close-deduct-modal.window="deductOpen = false"
    @open-block-modal.window="blockOpen = true"
    @close-block-modal.window="blockOpen = false"
    @open-add-funds-modal.window="addFundsOpen = true"
    @close-add-funds-modal.window="addFundsOpen = false"
    class="space-y-6 py-6"
>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-secondary-200 dark:border-secondary-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-secondary-900 dark:text-white tracking-tight">User Management</h2>
            <p class="text-[10px] uppercase font-bold text-secondary-400 tracking-wider mt-1">Manage balances, access and permissions</p>
        </div>
        <div class="bg-white dark:bg-secondary-900 rounded-xl shadow-sm border border-secondary-100 dark:border-secondary-800 p-1 flex items-center w-full md:w-72">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Search users..."
                    class="w-full pl-9 pr-4 py-2 text-xs border-none rounded-lg bg-transparent text-secondary-900 dark:text-white font-medium focus:ring-0 placeholder-secondary-400">
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-secondary-50 dark:bg-secondary-900/80 border-b border-secondary-100 dark:border-secondary-800">
                    <tr>
                        <th class="px-5 py-4 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">User</th>
                        <th class="px-5 py-4 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Balance</th>
                        <th class="px-5 py-4 text-center text-[10px] font-black uppercase tracking-widest text-secondary-400">Orders</th>
                        <th class="px-5 py-4 text-center text-[10px] font-black uppercase tracking-widest text-secondary-400">Status</th>
                        <th class="px-5 py-4 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Joined</th>
                        <th class="px-5 py-4 text-center text-[10px] font-black uppercase tracking-widest text-secondary-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-50 dark:divide-secondary-800/50">
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-secondary-50/50 dark:hover:bg-secondary-800/30 transition-colors {{ $user->is_blocked ? 'opacity-60' : '' }}">
                            <!-- User -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=f97316&color=fff&size=36"
                                        class="w-9 h-9 rounded-xl" alt="">
                                    <div>
                                        <p class="text-xs font-black text-secondary-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-[10px] text-secondary-400">{{ $user->email }}</p>
                                        @if($user->user_name)
                                            <p class="text-[9px] text-secondary-400">{{ '@' . $user->user_name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <!-- Balance -->
                            <td class="px-5 py-4">
                                <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ format_currency($user->balance) }}</span>
                            </td>
                            <!-- Orders -->
                            <td class="px-5 py-4 text-center">
                                <span class="px-2 py-1 bg-secondary-100 dark:bg-secondary-800 rounded-lg text-[10px] font-black text-secondary-600 dark:text-secondary-400">
                                    {{ $user->orders_count }}
                                </span>
                            </td>
                            <!-- Status -->
                            <td class="px-5 py-4 text-center">
                                @if($user->is_blocked)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Blocked
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @endif
                            </td>
                            <!-- Joined -->
                            <td class="px-5 py-4">
                                <span class="text-[10px] font-bold text-secondary-500">{{ $user->created_at->format('M d, Y') }}</span>
                            </td>
                            <!-- Actions -->
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Add Funds -->
                                    <button wire:click="openAddFunds({{ $user->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        Add
                                    </button>
                                    <!-- Deduct Balance -->
                                    <button wire:click="openDeduct({{ $user->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-[9px] font-black uppercase tracking-widest hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                        Deduct
                                    </button>
                                    <!-- Block / Unblock -->
                                    @if($user->is_blocked)
                                        <button wire:click="openBlock({{ $user->id }}, 'unblock')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Unblock
                                        </button>
                                    @else
                                        <button wire:click="openBlock({{ $user->id }}, 'block')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-[9px] font-black uppercase tracking-widest hover:bg-red-100 dark:hover:bg-red-900/40 transition-all">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Block
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <p class="text-[10px] font-black text-secondary-400 uppercase tracking-widest">No users found</p>
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

    <!-- ── Add Funds Modal ──────────────────────────────────── -->
    <div x-show="addFundsOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-secondary-950/60 backdrop-blur-sm"
        x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.away="addFundsOpen = false"
            class="bg-white dark:bg-secondary-900 w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl border border-secondary-100 dark:border-secondary-800"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-black text-secondary-900 dark:text-white uppercase tracking-tight">Add Funds</h3>
                <p class="text-xs text-secondary-500 mt-1">Manually credit balance to this user's account.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-500 mb-1.5">Amount</label>
                    <input wire:model="addFundsAmount" type="number" step="0.01" min="0.01" placeholder="0.00"
                        class="w-full px-4 py-3 rounded-xl border border-secondary-200 dark:border-secondary-700 bg-secondary-50 dark:bg-secondary-800 text-secondary-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none">
                    @error('addFundsAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-500 mb-1.5">Note (optional)</label>
                    <input wire:model="addFundsNote" type="text" placeholder="Reason for adding funds..."
                        class="w-full px-4 py-3 rounded-xl border border-secondary-200 dark:border-secondary-700 bg-secondary-50 dark:bg-secondary-800 text-secondary-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
            </div>

            <div class="flex flex-col gap-3 mt-6">
                <button wire:click="processAddFunds"
                    class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-600/20 transition-all active:scale-[0.98]">
                    Confirm & Add Funds
                </button>
                <button @click="addFundsOpen = false"
                    class="text-[10px] font-black text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-200 uppercase tracking-widest transition-colors py-2">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- ── Deduct Balance Modal ───────────────────────────────── -->
    <div x-show="deductOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-secondary-950/60 backdrop-blur-sm"
        x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.away="deductOpen = false"
            class="bg-white dark:bg-secondary-900 w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl border border-secondary-100 dark:border-secondary-800"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-black text-secondary-900 dark:text-white uppercase tracking-tight">Deduct Balance</h3>
                <p class="text-xs text-secondary-500 mt-1">Enter the amount to deduct from this user's balance.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-500 mb-1.5">Amount</label>
                    <input wire:model="deductAmount" type="number" step="0.01" min="0.01" placeholder="0.00"
                        class="w-full px-4 py-3 rounded-xl border border-secondary-200 dark:border-secondary-700 bg-secondary-50 dark:bg-secondary-800 text-secondary-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-amber-500 outline-none">
                    @error('deductAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-500 mb-1.5">Note (optional)</label>
                    <input wire:model="deductNote" type="text" placeholder="Reason for deduction..."
                        class="w-full px-4 py-3 rounded-xl border border-secondary-200 dark:border-secondary-700 bg-secondary-50 dark:bg-secondary-800 text-secondary-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
            </div>

            <div class="flex flex-col gap-3 mt-6">
                <button wire:click="processDeduct"
                    class="w-full py-4 bg-amber-600 hover:bg-amber-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-amber-600/20 transition-all active:scale-[0.98]">
                    Confirm Deduction
                </button>
                <button @click="deductOpen = false"
                    class="text-[10px] font-black text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-200 uppercase tracking-widest transition-colors py-2">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- ── Block / Unblock Modal ─────────────────────────────── -->
    <div x-show="blockOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-secondary-950/60 backdrop-blur-sm"
        x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.away="blockOpen = false"
            class="bg-white dark:bg-secondary-900 w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl border border-secondary-100 dark:border-secondary-800"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <div class="text-center">
                <div :class="$wire.blockAction === 'block' ? 'bg-red-100 dark:bg-red-900/30 text-red-600' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600'"
                    class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <template x-if="$wire.blockAction === 'block'">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </template>
                    <template x-if="$wire.blockAction === 'unblock'">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                </div>

                <h3 class="text-lg font-black text-secondary-900 dark:text-white uppercase tracking-tight mb-2">
                    <span x-text="$wire.blockAction === 'block' ? 'Block User' : 'Unblock User'"></span>
                </h3>
                <p class="text-xs text-secondary-500 font-medium leading-relaxed mb-8">
                    <span x-text="$wire.blockAction === 'block'
                        ? 'This user will no longer be able to log in or place orders.'
                        : 'This user will regain full access to the platform.'"></span>
                </p>

                <div class="flex flex-col gap-3">
                    <button wire:click="processBlock"
                        :class="$wire.blockAction === 'block' ? 'bg-red-600 hover:bg-red-500 shadow-red-600/20' : 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-600/20'"
                        class="w-full py-4 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg transition-all active:scale-[0.98]">
                        <span x-text="$wire.blockAction === 'block' ? 'Yes, Block User' : 'Yes, Unblock User'"></span>
                    </button>
                    <button @click="blockOpen = false"
                        class="text-[10px] font-black text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-200 uppercase tracking-widest transition-colors py-2">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
