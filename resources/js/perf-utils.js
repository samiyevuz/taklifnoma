/**
 * Shared performance & scroll-lock helpers
 */

export function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export function isCoarsePointer() {
    return window.matchMedia('(pointer: coarse)').matches;
}

export function throttle(fn, wait = 100) {
    let last = 0;
    let timer = null;

    return (...args) => {
        const now = Date.now();
        const remaining = wait - (now - last);

        if (remaining <= 0) {
            if (timer) {
                clearTimeout(timer);
                timer = null;
            }
            last = now;
            fn(...args);
        } else if (!timer) {
            timer = window.setTimeout(() => {
                last = Date.now();
                timer = null;
                fn(...args);
            }, remaining);
        }
    };
}

export function rafThrottle(fn) {
    let rafId = null;

    return (...args) => {
        if (rafId !== null) return;
        rafId = requestAnimationFrame(() => {
            rafId = null;
            fn(...args);
        });
    };
}

let scrollLockCount = 0;
let savedScrollY = 0;

export function lockBodyScroll() {
    if (scrollLockCount === 0) {
        savedScrollY = window.scrollY;
        document.documentElement.classList.add('scroll-locked');
        document.body.style.top = `-${savedScrollY}px`;
    }
    scrollLockCount += 1;
}

export function unlockBodyScroll() {
    if (scrollLockCount <= 0) return;
    scrollLockCount -= 1;
    if (scrollLockCount > 0) return;

    document.documentElement.classList.remove('scroll-locked');
    document.body.style.top = '';
    window.scrollTo(0, savedScrollY);
}

export function setButtonLoading(btn, loading, loadingText) {
    if (!btn) return;

    if (loading) {
        if (!btn.dataset.originalText) {
            btn.dataset.originalText = btn.textContent.trim();
        }
        btn.disabled = true;
        btn.classList.add('is-loading');
        if (loadingText) btn.textContent = loadingText;
    } else {
        btn.disabled = false;
        btn.classList.remove('is-loading');
        if (btn.dataset.originalText) {
            btn.textContent = btn.dataset.originalText;
        }
    }
}
