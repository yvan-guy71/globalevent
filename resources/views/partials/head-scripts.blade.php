<script>
    (function () {
        const stored = localStorage.getItem('theme');
        const root = document.documentElement;
        if (stored === 'dark') {
            root.classList.add('dark');
        } else if (stored === 'light') {
            root.classList.remove('dark');
        }
    })();

    document.addEventListener('DOMContentLoaded', function () {
        const root = document.documentElement;

        window.applyTheme = function (mode) {
            if (mode === 'dark') {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }
        };

        window.toggleTheme = function () {
            const next = root.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('theme', next);
            window.applyTheme(next);
        };

        window.toggleSettingsDropdown = function () {
            const menu = document.getElementById('settings-dropdown');
            const userMenu = document.getElementById('user-menu-dropdown');
            if (userMenu) {
                userMenu.classList.add('hidden');
            }
            if (menu) {
                menu.classList.toggle('hidden');
            }
        };

        window.toggleUserMenu = function () {
            const menu = document.getElementById('user-menu-dropdown');
            const settings = document.getElementById('settings-dropdown');
            if (settings) {
                settings.classList.add('hidden');
            }
            if (menu) {
                menu.classList.toggle('hidden');
            }
        };

        window.toggleMobileMenu = function () {
            const menu = document.getElementById('mobile-menu');
            const content = document.getElementById('mobile-menu-content');
            if (!menu || !content) return;

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => content.classList.remove('-translate-x-full'), 10);
            } else {
                content.classList.add('-translate-x-full');
                setTimeout(() => menu.classList.add('hidden'), 300);
            }
        };

        document.addEventListener('click', function (e) {
            const button = document.getElementById('settings-button');
            const menu = document.getElementById('settings-dropdown');
            if (!button || !menu || menu.classList.contains('hidden')) return;
            if (!menu.contains(e.target) && !button.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        document.addEventListener('click', function (e) {
            const button = document.getElementById('user-menu-button');
            const menu = document.getElementById('user-menu-dropdown');
            if (!button || !menu || menu.classList.contains('hidden')) return;
            if (!menu.contains(e.target) && !button.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenu) {
            mobileMenu.addEventListener('click', function (e) {
                if (e.target === mobileMenu) window.toggleMobileMenu();
            });
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    });
</script>
