@extends('layouts.admin')

@section('title', 'Create Service')
@section('page_title', 'Add New Service')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- API Provider -->
                <div>
                    <label for="api_provider_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        API Provider <span class="text-red-500">*</span>
                    </label>
                    <select name="api_provider_id" id="api_provider_id"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('api_provider_id') border-red-500 @enderror">
                        <option value="">Select a provider...</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" @selected(old('api_provider_id') == $provider->id)>
                                {{ $provider->api_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('api_provider_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('category_id') border-red-500 @enderror">
                        <option value="">Select a category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Name -->
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Service Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror"
                        placeholder="e.g., Instagram Followers, TikTok Likes">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Type -->
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Service Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('type') border-red-500 @enderror">
                        <option value="">Select type...</option>
                        <option value="default" @selected(old('type') === 'default')>Default</option>
                        <option value="subscriptions" @selected(old('type') === 'subscriptions')>Subscriptions</option>
                        <option value="custom_comments" @selected(old('type') === 'custom_comments')>Custom Comments</option>
                        <option value="mentions" @selected(old('type') === 'mentions')>Mentions</option>
                        <option value="package" @selected(old('type') === 'package')>Package</option>
                        <option value="comment_likes" @selected(old('type') === 'comment_likes')>Comment Likes</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rate (Price per 1K) -->
                <div>
                    <label for="rate" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Rate (per 1K) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="rate" id="rate" value="{{ old('rate') }}" step="0.0001"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('rate') border-red-500 @enderror"
                        placeholder="0.00">
                    @error('rate')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Order -->
                <div>
                    <label for="min_order" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Minimum Order <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="min_order" id="min_order" value="{{ old('min_order', 1) }}" min="1"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('min_order') border-red-500 @enderror">
                    @error('min_order')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Maximum Order -->
                <div>
                    <label for="max_order" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Maximum Order <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="max_order" id="max_order" value="{{ old('max_order', 100000) }}" min="1"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('max_order') border-red-500 @enderror">
                    @error('max_order')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Features -->
                <div class="col-span-2">
                    <fieldset class="space-y-3">
                        <legend class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Features</legend>
                        <div class="flex gap-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="dripfeed" value="1" @checked(old('dripfeed'))
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-brand-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Dripfeed Support</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="refill" value="1" @checked(old('refill'))
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-brand-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Refill Support</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="cancel" value="1" @checked(old('cancel'))
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-brand-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Cancel Support</span>
                            </label>
                        </div>
                    </fieldset>
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror"
                        placeholder="Service description...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-span-2">
                    <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('status') border-red-500 @enderror">
                        <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-medium">
                    Create Service
                </button>
                <a href="{{ route('admin.services.index') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
