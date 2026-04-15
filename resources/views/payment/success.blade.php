<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-secondary-800 overflow-hidden shadow-sm sm:rounded-lg text-center p-8">
                <div class="mb-4">
                    <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Payment Successful!</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Your transaction has been completed successfully and your balance has been updated.</p>
                
                <div class="bg-gray-50 dark:bg-secondary-900 p-4 rounded-lg text-left mb-6 inline-block w-full">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Transaction ID: <span class="font-mono text-gray-900 dark:text-white">{{ $request->input('basket_id') ?? 'N/A' }}</span></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Message: <span class="font-medium text-gray-900 dark:text-white">{{ $request->input('err_msg') ?? 'Success' }}</span></p>
                </div>

                <a href="{{ route('funds') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Return to Funds
                </a>
            </div>
        </div>
    </div>
</x-app-layout>