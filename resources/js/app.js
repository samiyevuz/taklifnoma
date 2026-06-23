/**
 * Taklifnoma — Premium Landing Interactions
 * Theme · Tilt · Slider · FAQ · GPU motion
 */

import { initRsvpLivePanels } from './rsvp-live-panel';
import { initCopyLinks } from './copy-link';
import { initLocaleDropdown } from './locale-dropdown';
import {
    prefersReducedMotion,
    throttle,
    lockBodyScroll,
    unlockBodyScroll,
    setButtonLoading,
} from './perf-utils';

const ELITE_EASE = 'cubic-bezier(0.16, 1, 0.3, 1)';
const THEME_COLORS = { light: '#FAF6F0', dark: '#0B0B0F' };

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

function updateThemeMeta(dark) {
    const meta = document.getElementById('meta-theme-color');
    if (meta) {
        meta.setAttribute('content', dark ? THEME_COLORS.dark : THEME_COLORS.light);
    }
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

    const applyTheme = (dark, animate = true) => {
        if (animate) {
            html.classList.add('theme-transition');
            window.setTimeout(() => html.classList.remove('theme-transition'), 400);
        }

        html.classList.toggle('dark', dark);
        toggles.forEach((btn) => {
            const labelDark = btn.dataset.themeLabelDark || 'Dark mode';
            const labelLight = btn.dataset.themeLabelLight || 'Light mode';
            const iconOnly = btn.dataset.themeIconOnly === 'true';

            btn.setAttribute('aria-pressed', String(dark));
            btn.setAttribute('aria-label', dark ? labelLight : labelDark);

            if (iconOnly) {
                btn.querySelector('.theme-icon-moon')?.classList.toggle('hidden', dark);
                btn.querySelector('.theme-icon-sun')?.classList.toggle('hidden', !dark);
            } else {
                btn.textContent = dark ? labelLight : labelDark;
            }
        });
        sessionStorage.setItem('theme', dark ? 'dark' : 'light');
        updateThemeMeta(dark);
    };

    applyTheme(isDark, false);

    toggles.forEach((btn) => {
        btn.addEventListener('click', () => applyTheme(!html.classList.contains('dark')));
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (sessionStorage.getItem('theme') !== null) return;
        applyTheme(e.matches, true);
    });
}

function initStickyNav() {
    const nav = document.getElementById('site-nav');
    if (!nav) return;

    const syncNavState = () => {
        nav.classList.toggle('is-scrolled', window.scrollY > 8);

        const height = Math.ceil(nav.getBoundingClientRect().height);
        if (height > 0) {
            document.documentElement.style.setProperty('--site-nav-height', `${height}px`);
        }
    };

    const onScroll = throttle(syncNavState, 80);

    syncNavState();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', throttle(syncNavState, 120), { passive: true });
}

function closeLocaleDropdowns() {
    document.querySelectorAll('[data-locale-dropdown].is-open').forEach((dropdown) => {
        dropdown.classList.remove('is-open');
        dropdown.querySelector('.locale-dropdown__trigger')?.setAttribute('aria-expanded', 'false');
        dropdown.querySelector('.locale-dropdown__menu')?.setAttribute('hidden', '');
    });
}

function initMobileNav() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const panel = document.getElementById('mobile-nav');
    const iconOpen = document.getElementById('menu-icon-open');
    const iconClose = document.getElementById('menu-icon-close');

    if (!toggle || !panel) return;

    let isOpen = false;

    const setOpen = (open) => {
        if (isOpen === open) return;
        isOpen = open;

        panel.classList.toggle('is-open', open);
        panel.setAttribute('aria-hidden', String(!open));
        toggle.setAttribute('aria-expanded', String(open));
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);

        if (open) {
            closeLocaleDropdowns();
            lockBodyScroll();
        } else {
            unlockBodyScroll();
        }
    };

    toggle.addEventListener('click', () => setOpen(!isOpen));
    panel.querySelectorAll('[data-close-mobile-nav]').forEach((el) => {
        el.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) setOpen(false);
    });

    return { close: () => setOpen(false) };
}

function initScrollReveal() {
    const elements = document.querySelectorAll('.reveal');
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

function initSmoothAnchors(mobileNav) {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            const id = anchor.getAttribute('href');
            if (!id || id === '#') return;

            const target = document.querySelector(id);
            if (!target) return;

            e.preventDefault();
            mobileNav?.close?.();
            const navHeight = document.getElementById('site-nav')?.offsetHeight ?? 80;
            const top = target.getBoundingClientRect().top + window.scrollY - navHeight - 16;
            window.scrollTo({ top, behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
        });
    });
}

function initMockupPhones() {
    const phones = [...document.querySelectorAll('[data-phone-mockup]')];
    if (!phones.length) return;

    if (prefersReducedMotion() || window.matchMedia('(pointer: coarse)').matches) return;

    const stage = document.getElementById('hero-mockup-stage');
    let stageVisible = true;

    if (stage && !prefersReducedMotion()) {
        const stageObserver = new IntersectionObserver(
            (entries) => {
                stageVisible = entries[0]?.isIntersecting ?? true;
            },
            { threshold: 0.05, rootMargin: '80px 0px' }
        );
        stageObserver.observe(stage);
    }

    const maxTilt = 9;

    phones.forEach((phone) => {
        let rafId = null;

        const resetTilt = () => {
            phone.classList.remove('is-tilting');
            phone.style.removeProperty('--phone-tilt-x');
            phone.style.removeProperty('--phone-tilt-y');
        };

        phone.addEventListener('mousemove', (event) => {
            if (!stageVisible) return;

            const rect = phone.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;

            phone.classList.add('is-tilting');

            if (rafId) cancelAnimationFrame(rafId);
            rafId = requestAnimationFrame(() => {
                phone.style.setProperty('--phone-tilt-x', `${-y * maxTilt}deg`);
                phone.style.setProperty('--phone-tilt-y', `${x * maxTilt}deg`);
            });
        });

        phone.addEventListener('mouseleave', resetTilt);
    });
}

function initTestimonialsSlider() {
    const slider = document.getElementById('testimonials-slider');
    if (!slider) return;

    const cards = [...slider.querySelectorAll('.testimonial-card')];
    const dots = [...slider.querySelectorAll('.testimonial-dot')];
    const prev = document.getElementById('testimonial-prev');
    const next = document.getElementById('testimonial-next');

    if (!cards.length) return;

    let current = 0;
    let autoplayTimer = null;
    let isVisible = true;

    const goTo = (index) => {
        current = (index + cards.length) % cards.length;

        cards.forEach((card, i) => {
            const active = i === current;
            card.classList.toggle('is-active', active);
            card.setAttribute('aria-hidden', String(!active));
        });

        dots.forEach((dot, i) => {
            const active = i === current;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-selected', String(active));
        });
    };

    const startAutoplay = () => {
        if (prefersReducedMotion() || !isVisible || document.hidden) return;
        stopAutoplay();
        autoplayTimer = window.setInterval(() => goTo(current + 1), 6000);
    };

    const stopAutoplay = () => {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    };

    prev?.addEventListener('click', () => {
        goTo(current - 1);
        startAutoplay();
    });

    next?.addEventListener('click', () => {
        goTo(current + 1);
        startAutoplay();
    });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            goTo(parseInt(dot.dataset.slideTo, 10));
            startAutoplay();
        });
    });

    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', startAutoplay);
    slider.addEventListener('focusin', stopAutoplay);
    slider.addEventListener('focusout', startAutoplay);

    const visibilityObserver = new IntersectionObserver(
        (entries) => {
            isVisible = entries[0]?.isIntersecting ?? true;
            if (isVisible) startAutoplay();
            else stopAutoplay();
        },
        { threshold: 0.15 }
    );
    visibilityObserver.observe(slider);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) stopAutoplay();
        else startAutoplay();
    });

    startAutoplay();
}

function initFaqAccordion() {
    const accordion = document.getElementById('faq-accordion');
    if (!accordion) return;

    const items = [...accordion.querySelectorAll('.faq-item')];

    items.forEach((item) => {
        const trigger = item.querySelector('.faq-trigger');
        const panel = item.querySelector('.faq-panel');
        if (!trigger || !panel) return;

        trigger.addEventListener('click', () => {
            const isOpen = item.classList.contains('is-open');

            items.forEach((other) => {
                const otherPanel = other.querySelector('.faq-panel');
                const otherTrigger = other.querySelector('.faq-trigger');
                other.classList.remove('is-open');
                otherPanel?.setAttribute('aria-hidden', 'true');
                otherTrigger?.setAttribute('aria-expanded', 'false');
            });

            if (!isOpen) {
                item.classList.add('is-open');
                panel.setAttribute('aria-hidden', 'false');
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
    });
}

function initRsvpPreview() {
    const panel = document.getElementById('rsvp-preview');
    if (!panel || prefersReducedMotion()) return;

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

function initAmbientPause() {
    const layer = document.querySelector('.ambient-layer');
    if (!layer || prefersReducedMotion()) return;

    const setPaused = (paused) => {
        layer.classList.toggle('is-paused', paused);
    };

    const observer = new IntersectionObserver(
        (entries) => setPaused(!entries[0]?.isIntersecting),
        { threshold: 0 }
    );
    observer.observe(layer);

    document.addEventListener('visibilitychange', () => {
        setPaused(document.hidden);
    });
}

function initPageLoader() {
    const loader = document.getElementById('page-loader');
    if (!loader) return;

    const finish = () => {
        document.documentElement.classList.add('is-page-ready');
        loader.classList.add('is-done');
        window.setTimeout(() => loader.remove(), 500);
    };

    if (document.readyState === 'complete') {
        finish();
    } else {
        window.addEventListener('load', finish, { once: true });
        window.setTimeout(finish, 4000);
    }
}

function initFavorites() {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    document.querySelectorAll('[data-favorite-btn]').forEach((btn) => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (btn.dataset.auth !== '1') {
                window.location.href = btn.dataset.loginUrl || '/login';
                return;
            }

            const slug = btn.dataset.templateSlug;
            const isActive = btn.classList.contains('is-active');
            const url = isActive ? `/favorites/${slug}` : `/favorites/${slug}`;
            const method = isActive ? 'DELETE' : 'POST';

            setButtonLoading(btn, true);

            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrf || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) throw new Error('favorite-failed');

                const data = await response.json();
                const favorited = Boolean(data.favorited);
                const icon = btn.querySelector('svg');

                btn.classList.toggle('is-active', favorited);
                btn.setAttribute('aria-pressed', String(favorited));
                btn.setAttribute(
                    'aria-label',
                    favorited ? 'Yoqtirganlardan olib tashlash' : 'Yoqtirganlarga saqlash'
                );

                if (icon) {
                    icon.setAttribute('fill', favorited ? 'currentColor' : 'none');
                }
            } catch {
                /* silent */
            } finally {
                setButtonLoading(btn, false);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initPageLoader();
    initRippleEffect();
    initThemeToggle();
    initLocaleDropdown();
    initStickyNav();
    const mobileNav = initMobileNav();
    initScrollReveal();
    initSmoothAnchors(mobileNav);
    initMockupPhones();
    initTestimonialsSlider();
    initFaqAccordion();
    initRsvpPreview();
    initAmbientPause();
    initRsvpLivePanels();
    initFavorites();
    initCopyLinks();
});
