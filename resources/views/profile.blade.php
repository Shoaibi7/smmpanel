<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-secondary-900 dark:text-white">{{ __('Profile Information') }}</h3>
                </x-slot>
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-secondary-900 dark:text-white">{{ __('Update Password') }}</h3>
                </x-slot>
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </x-card>

            <x-card class="border-red-100 dark:border-red-900/30">
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-red-600 dark:text-red-400">{{ __('Delete Account') }}</h3>
                </x-slot>
                <div class="max-w-xl text-red-600 dark:text-red-400">
                    <livewire:profile.delete-user-form />
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
