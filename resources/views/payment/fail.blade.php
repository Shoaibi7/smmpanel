<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-secondary-800 overflow-hidden shadow-sm sm:rounded-lg text-center p-8">
                <div class="mb-4">
                    <svg class="w-16 h-16 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-red-600 mb-2">Payment Failed!</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Unfortunately, your transaction could not be processed.</p>
                
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg text-left mb-6 inline-block w-full border border-red-100 dark:border-red-800">
                    <p class="text-sm text-red-500 dark:text-red-400">Error Code: <span class="font-mono text-gray-900 dark:text-white">{{ $request->input('err_code') ?? 'N/A' }}</span></p>
                    <p class="text-sm text-red-500 dark:text-red-400">Message: <span class="font-medium text-gray-900 dark:text-white">{{ $request->input('err_msg') ?? 'Unknown Error' }}</span></p>
                </div>

                <div class="space-x-4">
                    <a href="{{ route('funds') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Try Again
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>