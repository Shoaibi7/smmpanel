<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-secondary-900 dark:text-white leading-tight">
            @yield('page_title', 'Admin')
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </div>
</x-app-layout>
