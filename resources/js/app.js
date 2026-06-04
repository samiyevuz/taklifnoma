/**
 * Taklifnoma — Premium Landing Interactions
 * GPU: transform + opacity only · cubic-bezier(0.16, 1, 0.3, 1)
 */

const ELITE_EASE = 'cubic-bezier(0.16, 1, 0.3, 1)';

function initRippleEffect() {
    document.querySelectorAll('[data-ripple]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;

            const ripple = document.createElement('span');
            ripple.className = 'btn-premium__ripple';
            ripple.style.cssText = `width:${size}px;height:${size}px;left:${x}px;top:${y}px;`;
            button.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
        });
    });
}

function initThemeToggle() {
    const toggles = [
        document.getElementById('theme-toggle'),
        document.getElementById('theme-toggle-mobile'),
    ].filter(Boolean);

    if (!toggles.length) return;

    const html = document.documentElement;
    const stored = sessionStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = stored === 'dark' || (stored === null && prefersDark);

    const applyTheme = (dark) => {
        html.classList.toggle('dark', dark);
        toggles.forEach((btn) => {
            btn.setAttribute('aria-pressed', String(dark));
            btn.textContent = dark ? "Yorug' rejim" : "Qorong'u rejim";
        });
        sessionStorage.setItem('theme', dark ? 'dark' : 'light');
    };

    applyTheme(isDark);
    toggles.forEach((btn) => {
        btn.addEventListener('click', () => applyTheme(!html.classList.contains('dark')));
    });
}

function initStickyNav() {
    const nav = document.getElementById('site-nav');
    if (!nav) return;

    const onScroll = () => {
        nav.classList.toggle('is-scrolled', window.scrollY > 24);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

function initMobileNav() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const panel = document.getElementById('mobile-nav');
    const iconOpen = document.getElementById('menu-icon-open');
    const iconClose = document.getElementById('menu-icon-close');

    if (!toggle || !panel) return;

    const setOpen = (open) => {
        panel.classList.toggle('is-open', open);
        panel.setAttribute('aria-hidden', String(!open));
        toggle.setAttribute('aria-expanded', String(open));
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);
        document.body.style.overflow = open ? 'hidden' : '';
    };

    toggle.addEventListener('click', () => setOpen(!panel.classList.contains('is-open')));
    panel.querySelectorAll('[data-close-mobile-nav]').forEach((el) => {
        el.addEventListener('click', () => setOpen(false));
    });
}

function initScrollReveal() {
    const elements = document.querySelectorAll('.reveal');
    if (!elements.length) return;

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) {
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
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    elements.forEach((el) => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight * 0.92) {
            el.classList.add('is-visible');
        } else {
            observer.observe(el);
        }
    });
}

function initSmoothAnchors() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            const id = anchor.getAttribute('href');
            if (!id || id === '#') return;

            const target = document.querySelector(id);
            if (!target) return;

            e.preventDefault();
            const navHeight = document.getElementById('site-nav')?.offsetHeight ?? 80;
            const top = target.getBoundingClientRect().top + window.scrollY - navHeight - 16;

            window.scrollTo({ top, behavior: 'smooth' });
        });
    });
}

function initRsvpPreview() {
    const panel = document.getElementById('rsvp-preview');
    if (!panel) return;

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    const counts = {
        accepted: { el: panel.querySelector('[data-rsvp-count="accepted"]'), base: 142 },
        declined: { el: panel.querySelector('[data-rsvp-count="declined"]'), base: 18 },
        pending: { el: panel.querySelector('[data-rsvp-count="pending"]'), base: 37 },
    };

    const bar = document.getElementById('rsvp-bar-fill');
    const percent = document.getElementById('rsvp-percent');

    const animateValue = (el, from, to, duration = 1200) => {
        if (!el) return;
        const start = performance.now();
        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(from + (to - from) * eased);
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            if (!entries[0]?.isIntersecting) return;

            animateValue(counts.accepted.el, 0, counts.accepted.base);
            animateValue(counts.declined.el, 0, counts.declined.base);
            animateValue(counts.pending.el, 0, counts.pending.base);

            if (bar) {
                bar.style.width = '0%';
                requestAnimationFrame(() => {
                    bar.style.transition = `width 1.4s ${ELITE_EASE}`;
                    bar.style.width = '72%';
                });
            }
            if (percent) animateValue(percent, 0, 72);

            observer.disconnect();
        },
        { threshold: 0.3 }
    );

    observer.observe(panel);
}

document.addEventListener('DOMContentLoaded', () => {
    initRippleEffect();
    initThemeToggle();
    initStickyNav();
    initMobileNav();
    initScrollReveal();
    initSmoothAnchors();
    initRsvpPreview();
});
