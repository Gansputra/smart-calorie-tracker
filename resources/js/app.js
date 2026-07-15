import './bootstrap';

// Dark/Light Mode Theme Toggle
document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (!themeToggleBtn) return;

    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    // Update icons visibility based on current theme state
    const updateThemeToggleIcons = (isDark) => {
        if (isDark) {
            themeToggleLightIcon?.classList.remove('hidden');
            themeToggleDarkIcon?.classList.add('hidden');
        } else {
            themeToggleLightIcon?.classList.add('hidden');
            themeToggleDarkIcon?.classList.remove('hidden');
        }
    };

    // Initialize icons based on current theme class
    updateThemeToggleIcons(document.documentElement.classList.contains('dark'));

    // Listen for theme toggle click events
    themeToggleBtn.addEventListener('click', () => {
        const isCurrentlyDark = document.documentElement.classList.contains('dark');
        
        if (isCurrentlyDark) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            updateThemeToggleIcons(false);
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            updateThemeToggleIcons(true);
        }
    });
});
