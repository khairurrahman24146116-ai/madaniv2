// ============================================================
// Madani Al-Aziziyah — UI Interactivity (vanilla JS, no deps)
// ============================================================
//
// Bawaan browser/Tailwind saja: IntersectionObserver,
// View Transitions API (native cross-document via meta), dan
// CSS transitions.

// ------------------------------------------------------------
// 1. Reveal on scroll — elemen `.reveal` tampil saat masuk viewport
// ------------------------------------------------------------
(function () {
    const revealEls = document.querySelectorAll('.reveal');
    if (!revealEls.length) return;
    if (!('IntersectionObserver' in window)) {
        revealEls.forEach((el) => el.classList.add('is-visible'));
        return;
    }
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach((el) => observer.observe(el));
})();

// ------------------------------------------------------------
// 2. Animated counter — elemen dengan data-count="<angka>"
// ------------------------------------------------------------
(function () {
    if (!('IntersectionObserver' in window)) return;
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    const easeOut = (t) => 1 - Math.pow(1 - t, 3);
    const animate = (el) => {
        const raw = el.dataset.count;
        // Pisahkan prefix ("Rp "), angka, dan suffix ("%", "kwitansi", dll)
        const prefix = (raw.match(/^\D+/) || [''])[0];
        const suffix = (raw.match(/\D+$/) || [''])[0];
        const digits = raw.replace(/\D/g, '');
        const negative = raw.trim().startsWith('-');
        const target = parseInt(digits, 10) || 0;
        const thousands = /\.\d{3}/.test(raw) ? '.' : (/,?\d{3}/.test(raw) ? ',' : '');
        const decimalMatch = raw.match(/[.,](\d{1,2})(?!\d)/);
        const decimals = decimalMatch ? decimalMatch[1].length : 0;
        const duration = 900;
        const start = performance.now();
        const fmt = (val) => {
            const abs = Math.abs(Math.round(val));
            let s = String(abs);
            if (thousands) {
                s = s.replace(/\B(?=(\d{3})+(?!\d))/g, thousands);
            }
            if (decimals > 0) {
                const parts = (val % 1).toFixed(decimals).replace('-', '');
                s = s + '.' + parts;
            }
            return (negative ? '-' : '') + s;
        };
        const step = (now) => {
            const p = Math.min((now - start) / duration, 1);
            el.textContent = prefix + fmt(target * easeOut(p)) + suffix;
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = prefix + fmt(target) + suffix;
        };
        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                animate(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach((el) => observer.observe(el));
})();

// ------------------------------------------------------------
// 3. Ripple effect untuk tombol (opsional: class .ripple)
// ------------------------------------------------------------
(function () {
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.addEventListener('pointerdown', (e) => {
            const el = e.target.closest('.ripple');
            if (!el) return;
            const rect = el.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 2;
            const ripple = document.createElement('span');
            ripple.className = 'ripple-ink';
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            el.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    }
})();

// ============================================================
// Default behaviour (dari setup asli)
// ============================================================
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

    // Trigger reveal & counter untuk elemen yang sudah di viewport saat load
    requestAnimationFrame(() => {
        document.querySelectorAll('.reveal').forEach((el) => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) el.classList.add('is-visible');
        });
    });

    // Smooth anchor scroll
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
            }
        });
    });
});