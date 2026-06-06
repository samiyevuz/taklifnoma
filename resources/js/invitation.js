/**
 * Nikoh To'yi Premium — Invitation Template Viewer
 * Countdown · RSVP · Music · Scroll reveal · Map sheet
 */

import { initLocaleDropdown } from './locale-dropdown';

const INV_EASE = 'cubic-bezier(0.16, 1, 0.3, 1)';

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function pad(n) {
    return String(n).padStart(2, '0');
}

function initScrollReveal() {
    const elements = document.querySelectorAll('.inv-reveal');
    if (!elements.length) return;

    if (prefersReducedMotion()) {
        elements.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -30px 0px' }
    );

    elements.forEach((el) => observer.observe(el));
}

function initWelcomeScroll() {
    const btn = document.getElementById('inv-scroll-btn');
    const target = document.getElementById('inv-details');
    if (!btn || !target) return;

    btn.addEventListener('click', () => {
        target.scrollIntoView({ behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
    });
}

function initCountdown() {
    const container = document.getElementById('inv-countdown');
    if (!container) return;

    const targetStr = container.dataset.target;
    const target = new Date(targetStr).getTime();

    const els = {
        days: document.getElementById('cd-days'),
        hours: document.getElementById('cd-hours'),
        minutes: document.getElementById('cd-minutes'),
        seconds: document.getElementById('cd-seconds'),
    };

    const tick = () => {
        const now = Date.now();
        const diff = Math.max(0, target - now);

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        if (els.days) els.days.textContent = pad(days);
        if (els.hours) els.hours.textContent = pad(hours);
        if (els.minutes) els.minutes.textContent = pad(minutes);
        if (els.seconds) els.seconds.textContent = pad(seconds);

        if (diff <= 0 && timer) {
            clearInterval(timer);
        }
    };

    tick();
    const timer = setInterval(tick, 1000);
}

function initDressCode() {
    const swatches = document.querySelectorAll('.inv-dress-swatch');
    const noteEl = document.getElementById('inv-dress-note');
    if (!swatches.length || !noteEl) return;

    swatches.forEach((swatch) => {
        swatch.addEventListener('click', () => {
            swatches.forEach((s) => {
                s.classList.remove('is-active');
                s.setAttribute('aria-pressed', 'false');
            });
            swatch.classList.add('is-active');
            swatch.setAttribute('aria-pressed', 'true');

            const note = swatch.dataset.note || '';
            noteEl.style.opacity = '0';
            requestAnimationFrame(() => {
                setTimeout(() => {
                    noteEl.textContent = note;
                    noteEl.style.opacity = '1';
                }, 150);
            });
        });
    });
}

function initCounters() {
    document.querySelectorAll('.inv-counter').forEach((counter) => {
        const display = counter.querySelector('.inv-counter__value');
        const hiddenInput = document.getElementById(`rsvp-${counter.dataset.counter}`);
        const min = parseInt(counter.dataset.min, 10);
        const max = parseInt(counter.dataset.max, 10);
        let value = parseInt(display?.textContent || min, 10);

        counter.querySelectorAll('.inv-counter__btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                if (btn.dataset.action === 'increment' && value < max) value++;
                if (btn.dataset.action === 'decrement' && value > min) value--;

                if (display) display.textContent = value;
                if (hiddenInput) hiddenInput.value = value;
            });
        });
    });
}

function initRsvpForm() {
    const form = document.getElementById('inv-rsvp-form');
    const success = document.getElementById('inv-rsvp-success');
    const successTitle = document.getElementById('inv-rsvp-success-title');
    const successMsg = document.getElementById('inv-rsvp-success-msg');
    const statusInput = document.getElementById('rsvp-status');
    const guestsField = document.getElementById('rsvp-guests-field');
    const childrenField = document.getElementById('rsvp-children-field');
    const options = form?.querySelectorAll('.inv-rsvp-option');

    if (!form) return;

    options?.forEach((opt) => {
        opt.addEventListener('click', () => {
            options.forEach((o) => {
                o.classList.remove('is-selected');
                o.setAttribute('aria-pressed', 'false');
            });
            opt.classList.add('is-selected');
            opt.setAttribute('aria-pressed', 'true');
            if (statusInput) statusInput.value = opt.dataset.status;

            const attending = opt.dataset.status === 'attending';
            guestsField?.classList.toggle('hidden', !attending);
            childrenField?.classList.toggle('hidden', !attending);
        });
    });

    const submitBtn = document.getElementById('inv-submit');
    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content ||
        form.querySelector('input[name="_token"]')?.value;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('rsvp-name');
        if (!name?.value.trim()) {
            name?.focus();
            name?.setAttribute('aria-invalid', 'true');
            return;
        }
        name?.removeAttribute('aria-invalid');

        const rsvpUrl = form.dataset.rsvpUrl;
        if (!rsvpUrl) return;

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = i18n.submitting || 'Sending...';
        }

        const formData = new FormData(form);

        const showRsvpFeedback = (isError, title, message) => {
            if (successTitle) successTitle.textContent = title;
            if (successMsg) successMsg.textContent = message;
            success?.classList.toggle('is-error', isError);
            success?.classList.add('is-shown');
        };

        try {
            const response = await fetch(rsvpUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = payload.errors
                    ? Object.values(payload.errors).flat()[0]
                    : payload.message
                      || (response.status === 419 ? i18n.sessionExpired : null)
                      || i18n.error
                      || 'Error';
                showRsvpFeedback(
                    true,
                    i18n.errorTitle || 'Error',
                    firstError,
                );
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = i18n.submit || 'Confirm';
                }
                return;
            }

            showRsvpFeedback(
                false,
                i18n.thanks || 'Thank you!',
                payload.message || i18n.success || 'Received.',
            );

            form.style.display = 'none';
        } catch {
            showRsvpFeedback(
                true,
                i18n.errorTitle || 'Error',
                i18n.networkError || 'Network error.',
            );
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Tasdiqlash';
            }
        }
    });
}

function initSubmitRipple() {
    const btn = document.getElementById('inv-submit');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const ripple = document.createElement('span');
        ripple.className = 'inv-submit__ripple';
        ripple.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX - rect.left - size / 2}px;top:${e.clientY - rect.top - size / 2}px;`;
        this.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
    });
}

const MUSIC_TARGET_VOLUME = 0.42;
const i18n = window.invitationI18n || {};

function fadeAudioVolume(audio, target, duration = 1400) {
    return new Promise((resolve) => {
        const start = audio.volume;
        const diff = target - start;
        if (Math.abs(diff) < 0.01 || duration <= 0 || prefersReducedMotion()) {
            audio.volume = target;
            resolve();
            return;
        }

        const started = performance.now();
        const step = (now) => {
            const progress = Math.min(1, (now - started) / duration);
            const eased = 1 - Math.pow(1 - progress, 3);
            audio.volume = Math.max(0, Math.min(1, start + diff * eased));
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                resolve();
            }
        };
        requestAnimationFrame(step);
    });
}

function waitForAudioReady(audio, timeout = 12000) {
    if (audio.readyState >= HTMLMediaElement.HAVE_ENOUGH_DATA) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        const cleanup = () => {
            clearTimeout(timer);
            audio.removeEventListener('canplaythrough', onReady);
            audio.removeEventListener('loadeddata', onReady);
            audio.removeEventListener('error', onError);
        };
        const onReady = () => {
            cleanup();
            resolve();
        };
        const onError = () => {
            cleanup();
            reject(new Error('audio-load-failed'));
        };
        const timer = setTimeout(() => {
            cleanup();
            reject(new Error('audio-load-timeout'));
        }, timeout);

        audio.addEventListener('canplaythrough', onReady, { once: true });
        audio.addEventListener('loadeddata', onReady, { once: true });
        audio.addEventListener('error', onError, { once: true });
        audio.load();
    });
}

function initMusicPlayer() {
    const btn = document.getElementById('inv-music');
    const audio = document.getElementById('inv-audio');
    const iconPlay = document.getElementById('inv-music-icon-play');
    const iconPause = document.getElementById('inv-music-icon-pause');

    if (!btn || !audio) return;

    let isPlaying = false;
    let fadeToken = 0;

    audio.volume = 0;
    audio.loop = true;

    const setPlaying = (playing) => {
        isPlaying = playing;
        btn.classList.toggle('is-playing', playing);
        btn.setAttribute('aria-pressed', String(playing));
        btn.setAttribute(
            'aria-label',
            playing ? (i18n.musicPause || 'Pause music') : (i18n.musicPlay || 'Play music')
        );
        iconPlay?.classList.toggle('hidden', playing);
        iconPause?.classList.toggle('hidden', !playing);
    };

    const showMusicError = () => {
        btn.classList.add('is-error');
        btn.classList.remove('is-loading');
        btn.setAttribute('aria-label', i18n.musicError || 'Music failed to load.');
    };

    const stopMusic = async () => {
        const token = ++fadeToken;
        await fadeAudioVolume(audio, 0, 700);
        if (token !== fadeToken) return;
        audio.pause();
        setPlaying(false);
    };

    btn.addEventListener('click', async () => {
        if (isPlaying) {
            await stopMusic();
            return;
        }

        btn.classList.remove('is-error');
        btn.classList.add('is-loading');

        try {
            await waitForAudioReady(audio);
            audio.currentTime = 0;
            await audio.play();

            const token = ++fadeToken;
            await fadeAudioVolume(audio, MUSIC_TARGET_VOLUME);
            if (token !== fadeToken) return;

            btn.classList.remove('is-loading');
            setPlaying(true);
        } catch {
            audio.pause();
            audio.volume = 0;
            setPlaying(false);
            showMusicError();
        }
    });
}

function initLocationMap() {
    const container = document.getElementById('inv-location-map');
    if (!container || typeof window.L === 'undefined') return;

    const lat = Number(container.dataset.lat);
    const lng = Number(container.dataset.lng);

    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

    const map = window.L.map(container, {
        zoomControl: true,
        scrollWheelZoom: false,
    }).setView([lat, lng], 16);

    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    window.L.marker([lat, lng]).addTo(map);

    window.setTimeout(() => map.invalidateSize(), 300);
}

function initMapModal() {
    const openBtn = document.getElementById('inv-map-open');
    const closeBtn = document.getElementById('inv-map-close');
    const modal = document.getElementById('inv-map-modal');

    if (!openBtn || !modal) return;

    const setOpen = (open) => {
        modal.classList.toggle('is-open', open);
        modal.setAttribute('aria-hidden', String(!open));
        document.body.style.overflow = open ? 'hidden' : '';
    };

    openBtn.addEventListener('click', () => setOpen(true));
    closeBtn?.addEventListener('click', () => setOpen(false));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) setOpen(false);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initLocaleDropdown();
    initScrollReveal();
    initWelcomeScroll();
    initCountdown();
    initDressCode();
    initCounters();
    initRsvpForm();
    initSubmitRipple();
    initMusicPlayer();
    initMapModal();
    initLocationMap();
});
