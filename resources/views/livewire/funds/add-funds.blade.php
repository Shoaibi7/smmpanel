<?php

use Livewire\Volt\Component;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $paymentMethod = '';
    public string $phoneNumber   = '';
    public float  $amount        = 0;
    public string $bankName      = '';
    public string $accountTitle  = '';
    public string $transactionRef = '';

    public $paymentMethods = [
        'jazzcash'      => 'JazzCash',
        'easypaisa'     => 'Easypaisa',
        'bank_transfer' => 'Bank Transfer',
    ];

    public function mount()
    {
        // Load user's deposit history for display
    }

    public function with(): array
    {
        return [
            'deposits' => Deposit::where('user_id', Auth::id())
                ->latest()
                ->take(10)
                ->get(),
            'balance' => Auth::user()->balance,
        ];
    }

    public function submitDeposit()
    {
        $minAmount = get_currency_code() === 'PKR' ? (float)get_conversion_rate() : 1.0;

        $rules = [
            'paymentMethod' => 'required|in:jazzcash,easypaisa,bank_transfer',
            'phoneNumber'   => ['required', 'string', 'regex:/^[0-9\+\-\s]{7,20}$/'],
            'amount'        => 'required|numeric|min:' . $minAmount . '|max:1000000',
        ];
        if ($this->paymentMethod === 'bank_transfer') {
            $rules['bankName']       = ['required', 'string', 'min:2', 'max:50'];
            $rules['accountTitle']   = ['required', 'string', 'min:2', 'max:100'];
            $rules['transactionRef'] = ['required', 'string', 'min:4', 'max:50'];
        }
        $this->validate($rules, [
            'paymentMethod.required' => 'Please select a payment method.',
            'phoneNumber.required'   => 'Phone number is required.',
            'phoneNumber.regex'      => 'Enter a valid phone number.',
            'amount.required'        => 'Amount is required.',
            'amount.min'             => 'Minimum deposit amount is ' . (get_currency_code() === 'PKR' ? 'Rs ' . get_conversion_rate() : '$1') . '.',
            'bankName.required'      => 'Bank name is required.',
            'accountTitle.required'  => 'Account holder name is required.',
            'transactionRef.required'=> 'Transaction reference is required.',
        ]);

        // Convert the input amount (which is in the selected currency) back to base USD for storage
        $usdAmount = convert_to_usd($this->amount);

        Deposit::create([
            'user_id'        => Auth::id(),
            'payment_method' => $this->paymentMethod,
            'phone_number'   => $this->phoneNumber,
            'amount'         => $usdAmount,
            'status'         => 'pending',
            'admin_note'     => $this->paymentMethod === 'bank_transfer'
                ? 'Bank: ' . $this->bankName . ' | Account Title: ' . $this->accountTitle . ' | Ref: ' . $this->transactionRef
                : null,
        ]);

        $this->reset(['paymentMethod', 'phoneNumber', 'amount', 'bankName', 'accountTitle', 'transactionRef']);
        $this->dispatch('toast', message: 'Deposit request submitted! Awaiting approval.', type: 'success');
    }
}; ?>

@php $layout = 'app'; @endphp

<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-secondary-900 via-secondary-800 to-secondary-950 rounded-[2.5rem] p-6 sm:p-8 text-white shadow-2xl border border-secondary-800/50">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 blur-[80px] rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-primary-600/5 blur-[80px] rounded-full -ml-20 -mb-20"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black font-outfit tracking-tight mb-1.5 leading-none">
                        Add <span class="text-emerald-400">Funds</span>
                    </h2>
                    <p class="text-secondary-400 text-[9px] font-bold uppercase tracking-[0.2em]">Top up your account balance instantly</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-secondary-500 uppercase tracking-widest mb-1.5">Current Balance</p>
                    <h3 class="text-2xl font-black font-outfit text-white leading-none">{{ format_currency($balance) }}</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Deposit Form -->
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-secondary-900 rounded-[2.5rem] border border-secondary-100 dark:border-secondary-800 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-secondary-600 dark:text-secondary-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                        <span class="w-3 h-[2px] rounded-full bg-emerald-500"></span>
                        New Deposit Request
                    </h3>

                    <form wire:submit.prevent="submitDeposit" class="space-y-4">
                        <!-- Payment Method -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-secondary-400 uppercase tracking-widest ml-1">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach($paymentMethods as $key => $label)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" wire:model.live="paymentMethod" value="{{ $key }}" class="sr-only peer">
                                        <div class="p-3.5 rounded-2xl border-2 border-secondary-100 dark:border-secondary-800 bg-secondary-50 dark:bg-black/20 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all duration-200 flex flex-col items-center gap-2 group-hover:border-emerald-300 dark:group-hover:border-emerald-700">
                                            @if($key === 'jazzcash')
                                                <div class="w-8 h-8 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center peer-checked:scale-110 transition-transform">
                                                    <span class="text-sm font-black text-red-600 dark:text-red-400">JC</span>
                                                </div>
                                            @elseif($key === 'easypaisa')
                                                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                                    <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">EP</span>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                    <span class="text-sm font-black text-blue-600 dark:text-blue-400">BT</span>
                                                </div>
                                            @endif
                                            <span class="text-[9px] font-black text-secondary-700 dark:text-secondary-300 uppercase tracking-widest text-center leading-tight">{{ $label }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('paymentMethod') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                        </div>

                        @if($paymentMethod)
                        <!-- Account Details Banner -->
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-200 dark:border-amber-700/50">
                            <p class="text-[9px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Send Payment To
                            </p>
                            <div class="text-[11px] font-black text-amber-900 dark:text-amber-300">
                                @if($paymentMethod === 'jazzcash')
                                    JazzCash Account: <span class="font-mono">03009499712</span>
                                @elseif($paymentMethod === 'easypaisa')
                                    Easypaisa Account: <span class="font-mono">03009499712</span>
                                @else
                                    Bank: <span class="font-mono">HBL — 01234567890123</span>
                                @endif
                            </div>
                            <p class="text-[9px] text-amber-600 dark:text-amber-500 mt-1.5 font-bold">Enter YOUR number below and the amount you sent.</p>
                        </div>
                        @endif

                        @if($paymentMethod === 'bank_transfer')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-secondary-400 uppercase tracking-widest ml-1">Bank Name <span class="text-red-500">*</span></label>
                                <select wire:model="bankName" class="w-full px-4 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none">
                                    <option value="">Select bank</option>
                                    @foreach(['HBL','UBL','MCB','Meezan','Bank Alfalah','Allied Bank','Askari'] as $bnk)
                                        <option value="{{ $bnk }}">{{ $bnk }}</option>
                                    @endforeach
                                </select>
                                @error('bankName') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-secondary-400 uppercase tracking-widest ml-1">Account Title <span class="text-red-500">*</span></label>
                                <input wire:model="accountTitle" type="text" placeholder="Account holder name"
                                       class="w-full px-4 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none">
                                @error('accountTitle') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1.5 sm:col-span-2">
                                <label class="block text-[10px] font-black text-secondary-400 uppercase tracking-widest ml-1">Transaction Reference <span class="text-red-500">*</span></label>
                                <input wire:model="transactionRef" type="text" placeholder="Reference / IBAN / Slip no."
                                       class="w-full px-4 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none">
                                @error('transactionRef') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Phone Number -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-secondary-400 uppercase tracking-widest ml-1">
                                Your Phone / Account Number <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-secondary-400 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <input wire:model="phoneNumber" type="text" placeholder="e.g. 03001234567"
                                    class="w-full pl-11 pr-5 py-3.5 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-[1.25rem] text-xs font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none">
                            </div>
                            @error('phoneNumber') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Amount -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-secondary-400 uppercase tracking-widest ml-1">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-secondary-400 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <input wire:model.debounce.300ms="amount" type="number" step="0.01" min="1" placeholder="0.00"
                                    class="w-full pl-11 pr-5 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none">
                            </div>
                            @error('amount') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror

                            <!-- Quick Amount Buttons -->
                            <div class="flex gap-2 mt-2">
                                @foreach([300, 500, 1000, 1500] as $preset)
                                    <button type="button" wire:click="$set('amount', {{ $preset }})"
                                        class="flex-1 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg border border-secondary-100 dark:border-secondary-800 bg-secondary-50 dark:bg-black/20 text-secondary-600 dark:text-secondary-400 hover:border-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all">
                                        {{ $preset }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Summary -->
                        @if($amount > 0 && $paymentMethod)
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-800/50">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black text-secondary-500 dark:text-secondary-400 uppercase tracking-widest">You are depositing</span>
                                <span class="text-base font-black text-emerald-600 dark:text-emerald-400 font-outfit">{{ format_currency($amount) }}</span>
                            </div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-[10px] font-black text-secondary-500 dark:text-secondary-400 uppercase tracking-widest">via</span>
                                <span class="text-[10px] font-black text-secondary-900 dark:text-white uppercase tracking-widest">{{ $paymentMethods[$paymentMethod] ?? '' }}</span>
                            </div>
                        </div>
                        @endif

                        <button type="button" 
                            @click="$dispatch('open-modal', 'confirm-deposit')"
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-black text-[10px] uppercase tracking-[0.22em] shadow-xl shadow-emerald-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 group">
                            <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Submit Deposit Request</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Deposit History -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-secondary-900 rounded-[2.5rem] border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm">
                    <div class="px-6 py-5 border-b border-secondary-50 dark:border-secondary-800">
                        <h3 class="text-[10px] font-black text-secondary-900 dark:text-white uppercase tracking-widest">Deposit History</h3>
                        <p class="text-[8px] text-secondary-400 font-bold uppercase tracking-widest mt-0.5">Last 10 requests</p>
                    </div>

                    <div class="divide-y divide-secondary-50 dark:divide-secondary-800">
                        @forelse($deposits as $deposit)
                            <div class="px-6 py-4 flex items-center justify-between gap-3 hover:bg-secondary-50/50 dark:hover:bg-black/10 transition-colors">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black text-secondary-900 dark:text-white truncate">{{ $deposit->payment_method_label }}</p>
                                    <p class="text-[9px] text-secondary-400 font-bold font-mono truncate">{{ $deposit->phone_number }}</p>
                                    <p class="text-[8px] text-secondary-300 dark:text-secondary-600 font-bold mt-0.5">{{ $deposit->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-black font-outfit text-secondary-900 dark:text-white">{{ format_currency($deposit->amount) }}</p>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest
                                        @if($deposit->status === 'approved') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                                        @elseif($deposit->status === 'declined') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                                        @else bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400
                                        @endif">
                                        <span class="w-1 h-1 rounded-full
                                            @if($deposit->status === 'approved') bg-emerald-500
                                            @elseif($deposit->status === 'declined') bg-red-500
                                            @else bg-amber-500
                                            @endif"></span>
                                        {{ ucfirst($deposit->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-16 text-center">
                                <div class="w-12 h-12 bg-secondary-50 dark:bg-secondary-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                </div>
                                <p class="text-[9px] font-black text-secondary-400 uppercase tracking-[0.3em]">No deposits yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-data="{ open: false }" 
        @open-modal.window="if ($event.detail === 'confirm-deposit') open = true"
        @close-modal.window="open = false"
        x-show="open" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-secondary-950/60 backdrop-blur-sm"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <div @click.away="open = false" 
            class="bg-white dark:bg-secondary-900 w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl border border-secondary-100 dark:border-secondary-800 transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="text-center">
                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                
                <h3 class="text-lg font-black text-secondary-900 dark:text-white uppercase tracking-tight mb-2">Confirm Deposit</h3>
                <p class="text-xs text-secondary-500 font-medium leading-relaxed mb-8">
                    Are you sure you want to submit this deposit request? Please ensure you have sent the exact amount to our account.
                </p>

                <div class="flex flex-col gap-3">
                    <button @click="$wire.submitDeposit().then(() => open = false)" 
                            class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-600/20 transition-all active:scale-[0.98]">
                        Confirm & Submit
                    </button>
                    <button @click="open = false" 
                            class="text-[10px] font-black text-secondary-400 hover:text-secondary-600 dark:hover:text-secondary-200 uppercase tracking-widest transition-colors py-2">
                        Wait, go back
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
