<button
    type="button"
    onclick="window.toggleTheme()"
    class="theme-toggle-btn p-2 rounded-lg bg-secondary-100 dark:bg-secondary-800 text-secondary-500 dark:text-secondary-400 hover:bg-secondary-200 dark:hover:bg-secondary-700 focus:outline-none transition-colors duration-200"
>
    <!-- Sun Icon (Visible in dark mode) -->
    <svg class="theme-toggle-light-icon w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l.707.707M6.343 6.343l.707-.707M14.5 12a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
    </svg>
    <!-- Moon Icon (Visible in light mode) -->
    <svg class="theme-toggle-dark-icon w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
    </svg>
</button>

<script>
    (function() {
        if (window.themeToggleInitialized) {
            window.updateThemeIcons();
            return;
        }

        window.updateThemeIcons = function() {
            const storedTheme = localStorage.getItem('dark-mode');
            if (storedTheme === 'true') {
                document.documentElement.classList.add('dark');
            } else if (storedTheme === 'false') {
                document.documentElement.classList.remove('dark');
            }

            const isDark = document.documentElement.classList.contains('dark');
            const icons = {
                light: document.querySelectorAll('.theme-toggle-light-icon'),
                dark: document.querySelectorAll('.theme-toggle-dark-icon')
            };
            
            icons.light.forEach(icon => isDark ? icon.classList.remove('hidden') : icon.classList.add('hidden'));
            icons.dark.forEach(icon => isDark ? icon.classList.add('hidden') : icon.classList.remove('hidden'));
        };

        window.toggleTheme = function() {
            const isDark = !document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('dark-mode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('dark-mode', 'false');
            }
            window.updateThemeIcons();
        };

        window.themeToggleInitialized = true;
        
        // Initial sync
        window.updateThemeIcons();
        
        document.addEventListener('DOMContentLoaded', window.updateThemeIcons);
        document.addEventListener('livewire:navigated', window.updateThemeIcons);
    })();
</script>
