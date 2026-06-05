/**
 * Taklifnoma — Live RSVP dashboard polling
 */

const ELITE_EASE = 'cubic-bezier(0.16, 1, 0.3, 1)';

function formatUpdatedLabel(isoString) {
    if (!isoString) return '';
    const date = new Date(isoString);
    if (Number.isNaN(date.getTime())) return '';

    return date.toLocaleTimeString('uz-UZ', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
}

function animateCounter(el, nextValue) {
    if (!el) return;

    const current = parseInt(el.textContent || '0', 10) || 0;
    const target = parseInt(String(nextValue), 10) || 0;

    if (current === target) return;

    el.classList.add('is-updating');
    el.textContent = String(target);

    window.setTimeout(() => el.classList.remove('is-updating'), 450);
}

function buildFeedItem(item) {
    const li = document.createElement('li');
    li.className = 'rsvp-stat is-new';
    li.dataset.rsvpItem = '';
    li.dataset.rsvpId = String(item.id);

    const name = document.createElement('span');
    name.className = 'text-sm font-medium text-ink';
    name.textContent = item.guest_name;

    const status = document.createElement('span');
    status.className = `text-xs font-semibold ${item.is_attending ? 'text-luxury-emerald' : 'text-ink-muted'}`;
    status.textContent = item.guest_summary;

    li.append(name, status);

    window.setTimeout(() => li.classList.remove('is-new'), 900);

    return li;
}

function renderFeed(feed, items) {
    if (!feed) return;

    const emptyState = feed.querySelector('[data-rsvp-empty]');
    if (emptyState) emptyState.remove();

    feed.querySelectorAll('[data-rsvp-item]').forEach((node) => node.remove());

    items.forEach((item) => {
        feed.appendChild(buildFeedItem(item));
    });

    if (!items.length) {
        const empty = document.createElement('li');
        empty.className = 'rsvp-live-panel__empty';
        empty.dataset.rsvpEmpty = '';
        empty.textContent = feed.dataset.emptyLabel || 'Hali RSVP javoblari yo\'q.';
        feed.appendChild(empty);
    }
}

async function pollPanel(panel) {
    const url = panel.dataset.pollUrl;
    if (!url || document.hidden) return;

    const latestId = parseInt(panel.dataset.latestId || '0', 10) || 0;
    const endpoint = latestId > 0 ? `${url}?since_id=${latestId}` : url;

    try {
        const response = await fetch(endpoint, {
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

        animateCounter(panel.querySelector('[data-rsvp-count="attending"]'), data.attending);
        animateCounter(panel.querySelector('[data-rsvp-count="declined"]'), data.declined);
        animateCounter(panel.querySelector('[data-rsvp-count="guests"]'), data.total_guests);

        const percent = data.confirmation_rate ?? 0;
        const bar = panel.querySelector('[data-rsvp-bar]');
        const percentEl = panel.querySelector('[data-rsvp-percent]');

        if (bar) bar.style.width = `${percent}%`;
        if (percentEl) percentEl.textContent = String(percent);

        const feed = panel.querySelector('[data-rsvp-feed]');
        const recent = Array.isArray(data.recent) ? data.recent : [];

        if (recent.length) {
            const newestId = recent[0]?.id ?? latestId;
            const hasNewItems = newestId > latestId;

            if (hasNewItems || !feed?.querySelector('[data-rsvp-item]')) {
                renderFeed(feed, recent);
                panel.dataset.latestId = String(newestId);
            }
        }

        const updated = panel.querySelector('[data-rsvp-updated]');
        if (updated && data.fetched_at) {
            const time = formatUpdatedLabel(data.fetched_at);
            updated.textContent = time ? `Yangilandi: ${time}` : '';
        }

        if (data.has_new) {
            panel.classList.add('has-new-pulse');
            window.setTimeout(() => panel.classList.remove('has-new-pulse'), 1200);
        }
    } catch {
        // Silent retry on next interval
    }
}

function initRsvpLivePanel(panel) {
    const intervalMs = parseInt(panel.dataset.pollInterval || '8000', 10);
    const feed = panel.querySelector('[data-rsvp-feed]');

    if (feed) {
        feed.dataset.emptyLabel = feed.dataset.emptyLabel
            || panel.querySelector('[data-rsvp-empty]')?.textContent?.trim()
            || '';
    }

    const tick = () => pollPanel(panel);
    tick();

    const timer = window.setInterval(tick, intervalMs);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) tick();
    });

    window.addEventListener('beforeunload', () => window.clearInterval(timer));
}

export function initRsvpLivePanels() {
    document.querySelectorAll('[data-rsvp-live-panel]').forEach(initRsvpLivePanel);
}
