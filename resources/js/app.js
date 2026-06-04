/**
 * Taklifnoma — Global UI interactions
 * GPU-accelerated ripple · Theme toggle · Breakpoint indicator
 */

/**
 * Material-style ripple on premium CTA buttons
 */
function initRippleEffect() {
    document.querySelectorAll('[data-ripple]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;

            const ripple = document.createElement('span');
            ripple.className = 'btn-premium__ripple';
            ripple.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
            `;

            button.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
        });
    });
}

/**
 * Class-based dark mode toggle (persists in sessionStorage)
 */
function initThemeToggle() {
    const toggle = document.getElementById('theme-toggle');
    if (!toggle) {
        return;
    }

    const html = document.documentElement;
    const stored = sessionStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = stored === 'dark' || (stored === null && prefersDark);

    const applyTheme = (dark) => {
        html.classList.toggle('dark', dark);
        toggle.setAttribute('aria-pressed', String(dark));
        toggle.textContent = dark ? "Yorug' rejim" : "Qorong'u rejim";
        sessionStorage.setItem('theme', dark ? 'dark' : 'light');
    };

    applyTheme(isDark);

    toggle.addEventListener('click', () => {
        applyTheme(!html.classList.contains('dark'));
    });
}

/**
 * Dev preview: show current responsive breakpoint
 */
function initBreakpointIndicator() {
    const valueEl = document.getElementById('breakpoint-value');
    if (!valueEl) {
        return;
    }

    const getLabel = () => {
        const w = window.innerWidth;
        if (w >= 1536) return '2XL (1536px+)';
        if (w >= 1280) return 'XL (1280px+)';
        if (w >= 1024) return 'LG (1024px+)';
        if (w >= 768) return 'MD (768px+)';
        if (w >= 640) return 'SM (640px+)';
        if (w >= 320) return `Mobile (${w}px)`;
        return `Micro (${w}px)`;
    };

    const update = () => {
        valueEl.textContent = getLabel();
    };

    update();
    window.addEventListener('resize', update, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {
    initRippleEffect();
    initThemeToggle();
    initBreakpointIndicator();
});
