/**
 * Nikoh To'yi Premium — Invitation Template Viewer
 * Countdown · RSVP · Music · Scroll reveal · Map sheet
 */

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

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const name = document.getElementById('rsvp-name');
        if (!name?.value.trim()) {
            name?.focus();
            name?.setAttribute('aria-invalid', 'true');
            return;
        }
        name?.removeAttribute('aria-invalid');

        const status = statusInput?.value;
        const adults = document.getElementById('rsvp-adults')?.value || '1';

        if (successMsg) {
            if (status === 'attending') {
                successMsg.textContent = `Rahmat, ${name.value.trim()}! ${adults} kishi bilan kutib qolamiz.`;
            } else {
                successMsg.textContent = `Rahmat, ${name.value.trim()}. Sizni tushunamiz va yaxshi tilaklar tilaymiz.`;
            }
        }

        form.style.display = 'none';
        success?.classList.add('is-shown');
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

function createAmbientSynth() {
    let ctx = null;
    const nodes = [];

    return {
        start() {
            if (ctx) return;
            ctx = new AudioContext();
            [196, 246.94, 293.66].forEach((freq) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.value = 0.018;
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                nodes.push(osc);
            });
        },
        stop() {
            nodes.forEach((osc) => {
                try {
                    osc.stop();
                } catch {
                    /* already stopped */
                }
            });
            nodes.length = 0;
            if (ctx) {
                ctx.close();
                ctx = null;
            }
        },
    };
}

function initMusicPlayer() {
    const btn = document.getElementById('inv-music');
    const audio = document.getElementById('inv-audio');
    const iconPlay = document.getElementById('inv-music-icon-play');
    const iconPause = document.getElementById('inv-music-icon-pause');

    if (!btn) return;

    let isPlaying = false;
    let useSynth = false;
    const synth = createAmbientSynth();

    const setPlaying = (playing) => {
        isPlaying = playing;
        btn.classList.toggle('is-playing', playing);
        btn.setAttribute('aria-pressed', String(playing));
        btn.setAttribute(
            'aria-label',
            playing ? "Fon musiqasini o'chirish" : 'Fon musiqasini yoqish'
        );
        iconPlay?.classList.toggle('hidden', playing);
        iconPause?.classList.toggle('hidden', !playing);
    };

    const stopAll = () => {
        audio?.pause();
        synth.stop();
        setPlaying(false);
    };

    audio?.addEventListener('error', () => {
        useSynth = true;
    });

    btn.addEventListener('click', async () => {
        if (isPlaying) {
            stopAll();
            return;
        }

        try {
            if (!useSynth && audio?.querySelector('source')?.src) {
                await audio.play();
                setPlaying(true);
            } else {
                throw new Error('fallback');
            }
        } catch {
            useSynth = true;
            synth.start();
            setPlaying(true);
        }
    });
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
    initScrollReveal();
    initWelcomeScroll();
    initCountdown();
    initDressCode();
    initCounters();
    initRsvpForm();
    initSubmitRipple();
    initMusicPlayer();
    initMapModal();
});
