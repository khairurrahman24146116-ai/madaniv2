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
});
