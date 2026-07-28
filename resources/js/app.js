// Dark mode toggle
document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;
    const toggle = document.getElementById('dark-toggle');
    const icon = document.getElementById('theme-icon');
    const updateThemeIcon = () => {
        if (icon) icon.textContent = html.classList.contains('dark') ? 'light_mode' : 'dark_mode';
    };
    if (localStorage.getItem('theme') === 'dark') {
        html.classList.add('dark');
    }
    updateThemeIcon();
    if (toggle) {
        toggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
            updateThemeIcon();
        });
    }

    // User menu dropdown (avatar di TopAppBar)
    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userMenu = document.getElementById('user-menu');
    if (userMenuToggle && userMenu) {
        userMenuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !userMenu.classList.toggle('hidden');
            userMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        document.addEventListener('click', (e) => {
            if (!userMenu.classList.contains('hidden') && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
                userMenuToggle.setAttribute('aria-expanded', 'false');
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !userMenu.classList.contains('hidden')) {
                userMenu.classList.add('hidden');
                userMenuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
