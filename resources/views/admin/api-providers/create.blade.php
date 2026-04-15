@extends('layouts.admin')

@section('title', 'Create API Provider')
@section('page_title', 'Add New API Provider')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('admin.api-providers.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- API Name -->
            <div>
                <label for="api_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    API Provider Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="api_name" id="api_name" value="{{ old('api_name') }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('api_name') border-red-500 @enderror"
                    placeholder="e.g., SocialPanel, GetFollowers">
                @error('api_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Short Name -->
            <div>
                <label for="short_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Short Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name') }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('short_name') border-red-500 @enderror"
                    placeholder="e.g., socialpanel, getfollowers">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Used as unique identifier for this provider</p>
                @error('short_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- API URL -->
            <div>
                <label for="api_url" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    API Base URL <span class="text-red-500">*</span>
                </label>
                <input type="url" name="api_url" id="api_url" value="{{ old('api_url') }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('api_url') border-red-500 @enderror"
                    placeholder="https://api.provider.com/api/v2">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The API endpoint URL where requests will be sent</p>
                @error('api_url')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- API Key -->
            <div>
                <label for="api_key" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    API Key <span class="text-red-500">*</span>
                </label>
                <input type="password" name="api_key" id="api_key" value="{{ old('api_key') }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('api_key') border-red-500 @enderror"
                    placeholder="Enter your API authentication key">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your authentication key for API provider (will be encrypted)</p>
                @error('api_key')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('status') border-red-500 @enderror">
                    <option value="enabled" @selected(old('status') === 'enabled' || !old('status'))>Enabled</option>
                    <option value="disabled" @selected(old('status') === 'disabled')>Disabled</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-medium">
                    Create Provider
                </button>
                <a href="{{ route('admin.api-providers.index') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">API Integration Requirements</h4>
        <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
            <li>API must support standard SMM API format</li>
            <li>Balance endpoint: action=balance&key=YOUR_API_KEY</li>
            <li>Services endpoint: action=services&key=YOUR_API_KEY</li>
            <li>Must return JSON responses</li>
            <li>API timeout is set to 30 seconds</li>
        </ul>
    </div>
</div>

@endsection
