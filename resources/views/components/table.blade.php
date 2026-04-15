@props(['headers' => []])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-secondary-200 dark:divide-secondary-800']) }}>
        <thead class="bg-secondary-50 dark:bg-secondary-800/50">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-secondary-500 dark:text-secondary-400 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-secondary-900 divide-y divide-secondary-100 dark:divide-secondary-800">
            {{ $slot }}
        </tbody>
    </table>
</div>
