/*
 * Wizi Learn — interactions du thème back-office (adapté de Spark Admin).
 */
(function () {
    const MINIMIZED_KEY = 'wizi.sidebarMinimized';

    function storage(action, value) {
        try {
            return action === 'get'
                ? window.localStorage.getItem(MINIMIZED_KEY)
                : window.localStorage.setItem(MINIMIZED_KEY, value);
        } catch (e) {
            return null;
        }
    }

    function setMinimizedIcon(button) {
        const icon = button && button.querySelector('i');
        if (icon) {
            icon.className = document.body.classList.contains('sidebar-minimized')
                ? 'bi bi-chevron-bar-right'
                : 'bi bi-chevron-bar-left';
        }
    }

    // Appliqué au plus tôt pour éviter un « saut » du menu au chargement
    if (storage('get') === '1') {
        document.body.classList.add('sidebar-minimized');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.sidebar-wrapper');

        // Menu mobile + fond cliquable
        const toggleBtn = document.querySelector('#sidebar-toggle');
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show', sidebar.classList.contains('show'));
            });
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        // Réduction du menu sur grand écran (mémorisée)
        const desktopToggleBtn = document.querySelector('#desktop-sidebar-toggle');
        if (desktopToggleBtn) {
            setMinimizedIcon(desktopToggleBtn);
            desktopToggleBtn.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-minimized');
                storage('set', document.body.classList.contains('sidebar-minimized') ? '1' : '0');
                setMinimizedIcon(desktopToggleBtn);
                // Les graphiques se redimensionnent sur l'événement resize
                setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
            });
        }

        // Plein écran
        const fullscreenBtn = document.querySelector('#btn-fullscreen');
        if (fullscreenBtn) {
            const updateIcon = () => {
                const icon = fullscreenBtn.querySelector('i');
                if (icon) {
                    icon.className = document.fullscreenElement ? 'bi bi-fullscreen-exit' : 'bi bi-arrows-fullscreen';
                }
            };
            fullscreenBtn.addEventListener('click', function () {
                const action = document.fullscreenElement
                    ? document.exitFullscreen()
                    : document.documentElement.requestFullscreen();
                action.catch(err => console.error('Plein écran indisponible :', err.message));
            });
            document.addEventListener('fullscreenchange', updateIcon);
        }

        // Bouton retour en haut
        const backToTop = document.querySelector('.back-to-top');
        if (backToTop) {
            const toggleBackToTop = () => backToTop.classList.toggle('show', window.scrollY > 300);
            window.addEventListener('scroll', toggleBackToTop, { passive: true });
            toggleBackToTop();
            backToTop.addEventListener('click', function (e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
})();
