@extends('layouts.admin')

@section('title', 'Bulk Import Services')
@section('page_title', 'Import Services from ' . $provider->api_name)

@section('content')
<div class="max-w-2xl">
    <!-- Info Card -->
    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">About Bulk Import</h3>
        <p class="text-sm text-blue-800 dark:text-blue-200">
            This will fetch all available services from the <strong>{{ $provider->api_name }}</strong> API and import them into your database. 
            You can assign a default category to all imported services, or they will be created in their respective categories.
        </p>
    </div>

    <!-- Import Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('admin.services.bulk-import-store') }}" method="POST" onsubmit="handleImport(event)" class="space-y-6">
            @csrf

            <input type="hidden" name="api_provider_id" value="{{ $provider->id }}">

            <!-- Provider Info -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-300">Provider Name:</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $provider->api_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-300">Current Balance:</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">${{ $provider->formatted_balance }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-300">Current Services:</span>
                    <span class="font-semibold">{{ $provider->services_count }}</span>
                </div>
            </div>

            <!-- Category Selection -->
            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Default Category (Optional)
                </label>
                <select name="category_id" id="category_id"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    <option value="">Auto-assign by provider category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If you select a category, all imported services will be assigned to it</p>
            </div>

            <!-- Import Progress -->
            <div id="progressContainer" class="hidden">
                <div class="mb-2 flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Importing services...</span>
                    <span id="progressText" class="text-sm font-medium text-gray-600 dark:text-gray-400">0%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div id="progressBar" class="bg-brand-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
                <p id="progressMessage" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></p>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-medium">
                    Start Import
                </button>
                <a href="{{ route('admin.api-providers.show', $provider) }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Import Tips -->
    <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
        <h4 class="font-semibold text-gray-800 dark:text-white">Import Tips</h4>
        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-disc list-inside">
            <li>Make sure your API credentials are correct before importing</li>
            <li>Services will be created or updated based on their API service ID</li>
            <li>Duplicate services (same API ID) will be updated with new information</li>
            <li>The import process will sync the balance and services count</li>
            <li>Services will be marked as "active" by default</li>
            <li>You can disable individual services after import if needed</li>
        </ul>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function handleImport(event) {
        event.preventDefault();

        const form = event.target;
        const categoryId = document.getElementById('category_id').value;
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressMessage = document.getElementById('progressMessage');
        const submitBtn = document.getElementById('submitBtn');

        // Show progress container and hide submit button
        progressContainer.classList.remove('hidden');
        submitBtn.disabled = true;

        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            if (progress < 90) {
                progress += Math.random() * 20;
                if (progress > 90) progress = 90;
                progressBar.style.width = progress + '%';
                progressText.textContent = Math.floor(progress) + '%';
                progressMessage.textContent = 'Fetching services from API...';
            }
        }, 300);

        // Make the actual request
        showLoading();
        makeRequest('{{ route("admin.services.bulk-import-store") }}', 'POST', {
            api_provider_id: '{{ $provider->id }}',
            category_id: categoryId
        })
            .then(response => {
                clearInterval(progressInterval);
                progress = 100;
                progressBar.style.width = '100%';
                progressText.textContent = '100%';
                progressMessage.textContent = response.message + ' ✓';
                progressMessage.classList.add('text-green-600', 'dark:text-green-400');

                hideLoading();
                showToast(response.message, 'success');

                setTimeout(() => {
                    window.location.href = '{{ route("admin.api-providers.show", $provider) }}';
                }, 2000);
            })
            .catch(error => {
                clearInterval(progressInterval);
                hideLoading();
                progressContainer.classList.add('hidden');
                submitBtn.disabled = false;
                showToast(error.message || 'Import failed', 'error');
            });
    }
</script>
@endsection
