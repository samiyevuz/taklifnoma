export function initLocaleDropdown() {
    const dropdowns = document.querySelectorAll('[data-locale-dropdown]');
    if (!dropdowns.length) return;

    const closeAll = (except = null) => {
        dropdowns.forEach((dropdown) => {
            if (dropdown === except) return;

            dropdown.classList.remove('is-open');
            const trigger = dropdown.querySelector('.locale-dropdown__trigger');
            const menu = dropdown.querySelector('.locale-dropdown__menu');
            trigger?.setAttribute('aria-expanded', 'false');
            menu?.setAttribute('hidden', '');
        });
    };

    dropdowns.forEach((dropdown) => {
        const trigger = dropdown.querySelector('.locale-dropdown__trigger');
        const menu = dropdown.querySelector('.locale-dropdown__menu');
        if (!trigger || !menu) return;

        if (dropdown.dataset.localeBound === '1') return;
        dropdown.dataset.localeBound = '1';

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const willOpen = !dropdown.classList.contains('is-open');
            closeAll(willOpen ? dropdown : null);
            dropdown.classList.toggle('is-open', willOpen);
            trigger.setAttribute('aria-expanded', String(willOpen));
            menu.toggleAttribute('hidden', !willOpen);
        });
    });

    if (!document.documentElement.dataset.localeDropdownBound) {
        document.documentElement.dataset.localeDropdownBound = '1';
        document.addEventListener('click', () => closeAll());
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAll();
        });
    }
}
