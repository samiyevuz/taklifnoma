import { initRsvpLivePanels } from './rsvp-live-panel';

function formatNumber(value) {
    const num = Number(value) || 0;
    return new Intl.NumberFormat('uz-UZ').format(num);
}

async function pollAdminStats() {
    const grid = document.getElementById('admin-stats-grid');
    if (!grid || document.hidden) return;

    const url = grid.dataset.statsUrl;
    if (!url) return;

    try {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) return;

        const payload = await response.json();
        const data = payload?.data;
        if (!data) return;

        grid.querySelectorAll('[data-stat]').forEach((el) => {
            const key = el.dataset.stat;
            if (data[key] === undefined) return;

            const value = String(key).includes('revenue')
                ? formatNumber(data[key])
                : String(data[key]);

            if (el.textContent.trim() !== value) {
                el.textContent = value;
                el.classList.add('is-updating');
                window.setTimeout(() => el.classList.remove('is-updating'), 450);
            }
        });

        grid.querySelectorAll('[data-stat-meta]').forEach((el) => {
            const key = el.dataset.statMeta;
            if (data[key] === undefined) return;

            const prefix = String(key).includes('revenue') ? '+' : '+';
            const suffix = String(key).includes('revenue') ? " so'm" : '';
            el.textContent = `${prefix}${formatNumber(data[key])}${suffix}`;
        });
    } catch {
        // silent retry
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initRsvpLivePanels();

    const grid = document.getElementById('admin-stats-grid');
    if (!grid) return;

    pollAdminStats();
    window.setInterval(pollAdminStats, 30000);
});
