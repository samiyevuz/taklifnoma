export function initCopyLinks(root = document) {
    root.querySelectorAll('[data-copy-link-root]').forEach((container) => {
        container.querySelectorAll('[data-copy-btn]').forEach((button) => {
            if (button.dataset.copyBound === '1') return;
            button.dataset.copyBound = '1';

            button.addEventListener('click', async () => {
                const row = button.closest('.share-bar__row');
                const input = row?.querySelector('[data-copy-target]');
                const text = input?.value?.trim();

                if (!text) return;

                const defaultLabel = button.dataset.copyDefault || 'Copy';
                const successLabel = button.dataset.copySuccess || 'Copied!';

                try {
                    await navigator.clipboard.writeText(text);
                    button.textContent = successLabel;
                    button.classList.add('is-copied');

                    window.setTimeout(() => {
                        button.textContent = defaultLabel;
                        button.classList.remove('is-copied');
                    }, 2000);
                } catch {
                    if (input) {
                        input.focus();
                        input.select();
                        document.execCommand('copy');
                        button.textContent = successLabel;
                        window.setTimeout(() => {
                            button.textContent = defaultLabel;
                        }, 2000);
                    }
                }
            });
        });
    });
}
