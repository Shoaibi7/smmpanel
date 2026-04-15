<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithPagination;

    public string $filterStatus = '';
    public string $search       = '';
    public ?int    $confirmingId = null;
    public string  $actionType   = ''; // 'approve' or 'decline'

    public function with(): array
    {
        $query = Deposit::with('user')
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn($q) => $q->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            }))
            ->latest();

        return [
            'deposits'       => $query->paginate(15),
            'pendingCount'   => Deposit::where('status', 'pending')->count(),
            'totalApproved'  => Deposit::where('status', 'approved')->sum('amount'),
        ];
    }

    public function approve(int $depositId): void
    {
        $deposit = Deposit::findOrFail($depositId);

        if (!$deposit->isPending()) {
            $this->dispatch('toast', message: 'This deposit has already been processed.', type: 'error');
            return;
        }

        DB::transaction(function () use ($deposit) {
            $deposit->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Credit the user's balance
            $deposit->user->incrementBalance((float) $deposit->amount);
        });

        $this->dispatch('toast', message: 'Deposit approved and balance credited!', type: 'success');
    }

    public function decline(int $depositId): void
    {
        $deposit = Deposit::findOrFail($depositId);

        if (!$deposit->isPending()) {
            $this->dispatch('toast', message: 'This deposit has already been processed.', type: 'error');
            return;
        }

        $deposit->update([
            'status'      => 'declined',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('toast', message: 'Deposit request declined.', type: 'warning');
    }

    public function confirmAction(int $id, string $type): void
    {
        $this->confirmingId = $id;
        $this->actionType   = $type;
        $this->dispatch('open-admin-modal', type: $type);
    }

    public function processAction(): void
    {
        if ($this->actionType === 'approve') {
            $this->approve($this->confirmingId);
        } else {
            $this->decline($this->confirmingId);
        }
        $this->reset(['confirmingId', 'actionType']);
    }

    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }
}; ?>

@php $layout = 'app'; @endphp

<div x-data="{ modalOpen: false, actionType: '' }" 
     @open-admin-modal.window="actionType = $event.detail.type; modalOpen = true"
     @close-modal.window="modalOpen = false">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black text-secondary-900 dark:text-white mt-4 tracking-tight uppercase">Deposits Management</h2>
                <p class="text-[10px] uppercase font-bold text-secondary-400 tracking-wider mt-1">Review and approve user payment requests</p>
                <style>[x-cloak] { display: none !important; }</style>
            </div>
        </div>
    </x-slot>

    <div class="py-8 space-y-6">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Pending Approvals</p>
                <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $pendingCount }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Total Approved</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ format_currency($totalApproved) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Total Deposits</p>
                <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $deposits->total() }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by user name or email..."
                    class="w-full pl-9 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <select wire:model.live="filterStatus"
                class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="declined">Declined</option>
            </select>
        </div>

        <!-- Deposits Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-5 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">User</th>
                            <th class="px-5 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Method</th>
                            <th class="px-5 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Phone</th>
                            <th class="px-5 py-4 text-right text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Amount</th>
                            <th class="px-5 py-4 text-center text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-5 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Submitted</th>
                            <th class="px-5 py-4 text-center text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($deposits as $deposit)
                            <tr wire:key="deposit-{{ $deposit->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <!-- User -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($deposit->user->name) }}&background=f97316&color=fff&size=32"
                                            class="w-8 h-8 rounded-lg" alt="">
                                        <div class="min-w-0">
                                            <p class="text-xs font-black text-gray-900 dark:text-white truncate">{{ $deposit->user->name }}</p>
                                            <p class="text-[10px] text-gray-400 truncate">{{ $deposit->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <!-- Method -->
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest
                                        @if($deposit->payment_method === 'jazzcash') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                                        @elseif($deposit->payment_method === 'easypaisa') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                                        @else bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                        @endif">
                                        {{ $deposit->payment_method_label }}
                                    </span>
                                </td>
                                <!-- Phone -->
                                <td class="px-5 py-4">
                                    <span class="text-xs font-mono font-bold text-gray-700 dark:text-gray-300">{{ $deposit->phone_number }}</span>
                                </td>
                                <!-- Amount -->
                                <td class="px-5 py-4 text-right">
                                    <span class="text-sm font-black text-gray-900 dark:text-white font-outfit">{{ format_currency($deposit->amount) }}</span>
                                </td>
                                <!-- Status -->
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                        @if($deposit->status === 'approved') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                                        @elseif($deposit->status === 'declined') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                                        @else bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400
                                        @endif">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            @if($deposit->status === 'approved') bg-emerald-500
                                            @elseif($deposit->status === 'declined') bg-red-500
                                            @else bg-amber-500 animate-pulse
                                            @endif"></span>
                                        {{ ucfirst($deposit->status) }}
                                    </span>
                                </td>
                                <!-- Submitted -->
                                <td class="px-5 py-4">
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">{{ $deposit->created_at->format('M d, Y') }}</span>
                                    <p class="text-[9px] text-gray-400">{{ $deposit->created_at->format('h:i A') }}</p>
                                </td>
                                <!-- Actions -->
                                <td class="px-5 py-4 text-center">
                                    @if($deposit->isPending())
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="confirmAction({{ $deposit->id }}, 'approve')"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[9px] font-black uppercase tracking-widest rounded-lg transition-all active:scale-95 shadow-sm shadow-emerald-600/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <span wire:loading.remove wire:target="confirmAction({{ $deposit->id }}, 'approve')">Approve</span>
                                                <span wire:loading wire:target="confirmAction({{ $deposit->id }}, 'approve')">...</span>
                                            </button>
                                            <button wire:click="confirmAction({{ $deposit->id }}, 'decline')"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1.5 bg-red-100 dark:bg-red-900/30 hover:bg-red-600 hover:text-white text-red-700 dark:text-red-400 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all active:scale-95 disabled:opacity-50">
                                                <span wire:loading.remove wire:target="confirmAction({{ $deposit->id }}, 'decline')">Decline</span>
                                                <span wire:loading wire:target="confirmAction({{ $deposit->id }}, 'decline')">...</span>
                                            </button>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold">
                                                {{ $deposit->status === 'approved' ? '✓ Credited' : '✗ Rejected' }}
                                            </p>
                                            @if($deposit->approvedBy)
                                                <p class="text-[8px] text-gray-400">by {{ $deposit->approvedBy->name }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No deposits found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($deposits->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $deposits->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Admin Confirmation Modal -->
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
            class="bg-white dark:bg-secondary-900 w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl border border-secondary-100 dark:border-secondary-800 transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="text-center">
                <template x-if="actionType === 'approve'">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </template>
                <template x-if="actionType === 'decline'">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-red-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                </template>
                
                <h3 class="text-lg font-black text-secondary-900 dark:text-white uppercase tracking-tight mb-2">
                    <span x-text="actionType === 'approve' ? 'Approve Deposit' : 'Decline Deposit'"></span>
                </h3>
                <p class="text-xs text-secondary-500 font-medium leading-relaxed mb-8">
                    Are you sure you want to <span x-text="actionType"></span> this deposit request? 
                    <template x-if="actionType === 'approve'">
                        <span>The user's balance will be credited instantly.</span>
                    </template>
                </p>

                <div class="flex flex-col gap-3">
                    <button @click="$wire.processAction().then(() => modalOpen = false)" 
                            :class="actionType === 'approve' ? 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-600/20' : 'bg-red-600 hover:bg-red-500 shadow-red-600/20'"
                            class="w-full py-4 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg transition-all active:scale-[0.98]">
                        Confirm <span x-text="actionType"></span>
                    </button>
                    <button @click="modalOpen = false" 
                            class="text-[10px] font-black text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-200 uppercase tracking-widest transition-colors py-2">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
