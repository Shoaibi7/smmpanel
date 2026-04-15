<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 dark:hover:bg-primary-400 focus:bg-primary-700 dark:focus:bg-primary-600 active:bg-primary-900 dark:active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-secondary-900 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
