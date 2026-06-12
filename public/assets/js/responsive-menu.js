(function () {
    const body = document.body;
    const toggle = document.querySelector('[data-mobile-menu-toggle]');
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.querySelector('[data-sidebar-overlay]');

    if (!toggle || !sidebar || !overlay) {
        return;
    }

    function setMenuState(isOpen) {
        body.classList.toggle('sidebar-open', isOpen);
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    toggle.addEventListener('click', function () {
        setMenuState(!body.classList.contains('sidebar-open'));
    });

    overlay.addEventListener('click', function () {
        setMenuState(false);
    });

    sidebar.addEventListener('click', function (event) {
        if (event.target.closest('a')) {
            setMenuState(false);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setMenuState(false);
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            setMenuState(false);
        }
    });
})();
