<?php

use Livewire\Volt\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $apiEndpoint = '';
    public string $apiToken    = '';

    public function mount(): void
    {
        $this->apiEndpoint = \App\Models\Setting::where('key', 'api_endpoint')->value('value') ?? url('api/v1');

        $user = Auth::user();

        // Auto-generate token if user doesn't have one yet
        if (empty($user->api_token_key)) {
            $user->update(['api_token_key' => \Illuminate\Support\Str::random(64)]);
            $user->refresh();
        }

        $this->apiToken = $user->api_token_key;
    }
}; ?>

@php $layout = 'app'; @endphp

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-black text-secondary-900 dark:text-white uppercase tracking-tight">API Access</h2>
        <p class="text-[10px] text-secondary-500 font-bold uppercase tracking-widest mt-1">Integrate our SMM services into your platform</p>
    </div>

    <!-- Credentials -->
    <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 p-6 shadow-sm space-y-5">
        <h3 class="text-xs font-black text-secondary-900 dark:text-white uppercase tracking-widest">Your API Credentials</h3>

        <!-- Endpoint -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">API Endpoint</label>
            <div class="flex items-center gap-2">
                <input type="text" value="{{ $apiEndpoint }}" readonly
                    class="flex-1 px-4 py-2.5 bg-secondary-50 dark:bg-secondary-800 border border-secondary-200 dark:border-secondary-700 rounded-xl text-sm font-mono text-secondary-900 dark:text-white focus:outline-none">
                <button onclick="navigator.clipboard.writeText('{{ $apiEndpoint }}'); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy',2000)"
                    class="px-4 py-2.5 bg-secondary-100 dark:bg-secondary-700 hover:bg-secondary-200 text-secondary-700 dark:text-secondary-300 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all flex-shrink-0">
                    Copy
                </button>
            </div>
        </div>

        <!-- Token -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">Your API Token</label>
            <div class="flex items-center gap-2">
                <input type="text" value="{{ $apiToken }}" readonly
                    class="flex-1 px-4 py-2.5 bg-secondary-50 dark:bg-secondary-800 border border-secondary-200 dark:border-secondary-700 rounded-xl text-sm font-mono text-secondary-900 dark:text-white focus:outline-none">
                <button onclick="navigator.clipboard.writeText('{{ $apiToken }}'); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy',2000)"
                    class="px-4 py-2.5 bg-primary-600 hover:bg-primary-500 text-white text-[9px] font-black uppercase tracking-widest rounded-xl transition-all flex-shrink-0">
                    Copy
                </button>
            </div>
            <div class="mt-2">
                <p class="text-[9px] text-secondary-400 font-bold">
                    Use this token to authenticate all your API requests.
                </p>
            </div>
        </div>
    </div>

    <!-- Endpoints Reference -->
    <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 p-6 shadow-sm space-y-5">
        <h3 class="text-xs font-black text-secondary-900 dark:text-white uppercase tracking-widest">Available Endpoints</h3>

        <div class="rounded-xl border border-secondary-100 dark:border-secondary-800 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-secondary-50 dark:bg-secondary-900/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Method</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Endpoint</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-50 dark:divide-secondary-800/50">
                    @foreach([
                        ['GET',  '/services',                          'List all active services'],
                        ['GET',  '/balance',                           'Get your current balance'],
                        ['GET',  '/orders?per_page=15',                'List your orders (paginated)'],
                        ['GET',  '/order-status?order=API_ORDER_ID',   'Get status of an order'],
                        ['POST', '/add-order',                         'Place a new order'],
                        ['POST', '/cancel-order',                      'Cancel a pending/processing order'],
                        ['POST', '/refill-order',                      'Request a refill on an order'],
                    ] as [$method, $path, $desc])
                    <tr>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase
                                {{ $method === 'GET' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' }}">
                                {{ $method }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs font-mono text-secondary-700 dark:text-secondary-300">{{ $apiEndpoint }}{{ $path }}?key=TOKEN</code>
                        </td>
                        <td class="px-4 py-3 text-[10px] text-secondary-500 font-bold">{{ $desc }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Services -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">GET /services — Response</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">[{ "service":1,"name":"Instagram Followers","type":"default","category":"Instagram","rate":"0.85","min":100,"max":100000,"dripfeed":false,"refill":true,"cancel":false,"average_time":null }]</pre>
        </div>

        <!-- Balance -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">GET /balance — Response</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{ "balance": "500.00", "currency": "PKR" }</pre>
        </div>

        <!-- Orders -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">GET /orders — Response</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{
  "data": [{ "order":42,"api_order_id":"3977942","service":1,"status":"processing","quantity":1000,"start_count":500,"remains":500,"charge":"0.85","link":"https://...","created_at":"2026-04-26T10:00:00Z" }],
  "current_page":1,"last_page":3,"per_page":15,"total":42
}</pre>
        </div>

        <!-- Order Status -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">GET /order-status — Response</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{ "order":42,"api_order_id":"3977942","status":"processing","start_count":500,"remains":500,"quantity":1000,"charge":"0.85","link":"https://...","created_at":"2026-04-26T10:00:00Z" }</pre>
        </div>

        <!-- Add Order -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">POST /add-order — Request Body</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{
  "service": 1,
  "link": "https://instagram.com/p/example",
  "quantity": 1000
}</pre>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">POST /add-order — Response (201)</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{ "order":43,"api_order_id":"3977999","status":"processing","service":1,"quantity":1000,"charge":"0.85","link":"https://...","created_at":"2026-04-26T10:05:00Z" }</pre>
        </div>

        <!-- Cancel Order -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">POST /cancel-order — Request Body</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{ "order": 43 }</pre>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">POST /cancel-order — Response</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{ "order": 43, "status": "cancelled" }</pre>
        </div>

        <!-- Refill Order -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">POST /refill-order — Request Body</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{ "order": 42 }</pre>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">POST /refill-order — Response</label>
            <pre class="px-4 py-4 bg-secondary-900 dark:bg-black rounded-xl text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed">{ "order": 42, "refill_id": "12345", "status": "refill_requested" }</pre>
        </div>

        <!-- Ready URL -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-secondary-400 mb-1.5">Your Ready-to-use URL</label>
            <div class="flex items-center gap-2">
                <code class="flex-1 px-4 py-2.5 bg-secondary-50 dark:bg-secondary-800 border border-secondary-200 dark:border-secondary-700 rounded-xl text-xs font-mono text-secondary-700 dark:text-secondary-300 break-all">
                    {{ $apiEndpoint }}/services?key={{ $apiToken }}
                </code>
                <button onclick="navigator.clipboard.writeText('{{ $apiEndpoint }}/services?key={{ $apiToken }}')"
                    class="px-4 py-2.5 bg-primary-600 hover:bg-primary-500 text-white text-[9px] font-black uppercase tracking-widest rounded-xl transition-all flex-shrink-0">
                    Copy
                </button>
            </div>
            <a href="{{ $apiEndpoint }}/services?key={{ $apiToken }}" target="_blank"
                class="inline-flex items-center gap-1.5 mt-2 text-[10px] font-black text-primary-600 hover:underline uppercase tracking-widest">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Test in browser →
            </a>
        </div>
    </div>
</div>
