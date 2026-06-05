/**
 * Taklifnoma — Interactive Invitation Builder Studio
 * Live preview · Stepper · Mobile sheet · Checkout modal
 */

const ELITE_EASE = 'cubic-bezier(0.16, 1, 0.3, 1)';
const UZ_MONTHS = [
    '', 'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun',
    'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr',
];

function pad2(value) {
    return String(value).padStart(2, '0');
}

function formatEventDate(value) {
    if (!value) return '';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '';
    return `${date.getDate()} ${UZ_MONTHS[date.getMonth() + 1]} ${date.getFullYear()}`;
}

function formatEventTime(value) {
    if (!value) return '';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '';
    return `Soat ${pad2(date.getHours())}:${pad2(date.getMinutes())}`;
}

function initBuilderStudio() {
    const studio = document.getElementById('builder-studio');
    if (!studio) return;

    const bootstrap = JSON.parse(studio.dataset.bootstrap || '{}');
    const form = document.getElementById('builder-form');
    if (!form) return;

    let currentStep = 1;
    const totalSteps = 3;
    let dressColors = Array.isArray(bootstrap.dress_colors) ? [...bootstrap.dress_colors] : [];
    let rsvpEnabled = Boolean(bootstrap.rsvp_enabled ?? true);
    let previewRaf = null;
    let countdownTimer = null;

    const steps = [...studio.querySelectorAll('.builder-step')];
    const stepIndicators = [...studio.querySelectorAll('[data-step-indicator]')];
    const backBtn = document.getElementById('builder-back');
    const nextBtn = document.getElementById('builder-next');
    const previewPanel = document.getElementById('builder-preview-panel');
    const previewCol = studio.querySelector('.builder-studio__preview-col');
    const previewSheet = document.getElementById('builder-preview-sheet');
    const previewSheetMount = document.getElementById('builder-preview-sheet-mount');
    const previewFab = document.getElementById('builder-preview-fab');
    const checkoutModal = document.getElementById('builder-checkout-modal');
    const dressPalette = document.getElementById('dress-palette');
    const dressJsonInput = document.getElementById('dress_colors_json');
    const rsvpInput = document.getElementById('rsvp_enabled');
    const publishInput = document.getElementById('publish_flag');
    const rsvpToggle = document.getElementById('rsvp-toggle');
    const musicPreset = document.getElementById('music_preset');
    const musicUrlWrap = document.getElementById('music-url-wrap');
    const musicUrlInput = document.getElementById('music_url');
    const reviewList = studio.querySelector('.builder-review__list');

    const previewMap = {};
    studio.querySelectorAll('[data-preview]').forEach((el) => {
        const key = el.dataset.preview;
        if (!previewMap[key]) previewMap[key] = [];
        previewMap[key].push(el);
    });

    const setPreviewText = (key, value) => {
        (previewMap[key] || []).forEach((el) => {
            el.textContent = value || '';
        });
    };

    const togglePreviewBlock = (key, visible) => {
        (previewMap[key] || []).forEach((el) => {
            el.classList.toggle('hidden', !visible);
        });
    };

    const readFormState = () => ({
        groom_name: form.groom_name?.value?.trim() || '',
        bride_name: form.bride_name?.value?.trim() || '',
        event_type: form.event_type?.value?.trim() || '',
        event_at: form.event_at?.value || '',
        event_city: form.event_city?.value?.trim() || '',
        venue_name: form.venue_name?.value?.trim() || '',
        venue_address: form.venue_address?.value?.trim() || '',
        invitation_text_1: form.invitation_text_1?.value?.trim() || '',
        invitation_text_2: form.invitation_text_2?.value?.trim() || '',
        family_signature: form.family_signature?.value?.trim() || '',
    });

    const renderDressPreview = () => {
        const grid = document.getElementById('builder-preview-dress');
        if (!grid) return;

        grid.innerHTML = dressColors.map((color, index) => `
            <button type="button" class="inv-dress-swatch ${index === 0 ? 'is-active' : ''}" role="listitem" aria-label="${color.name}" style="pointer-events:none">
                <span class="inv-dress-swatch__circle" style="background-color:${color.hex}"></span>
                <span class="inv-dress-swatch__label">${color.name}</span>
            </button>
        `).join('');
    };

    const renderDressPalette = () => {
        if (!dressPalette) return;

        dressPalette.innerHTML = dressColors.map((color, index) => `
            <button
                type="button"
                class="builder-color-chip ${index === 0 ? 'is-active' : ''}"
                data-color-index="${index}"
                role="listitem"
                aria-label="${color.name}"
                aria-pressed="${index === 0 ? 'true' : 'false'}"
            >
                <span class="builder-color-chip__dot" style="background:${color.hex}"></span>
                <span class="builder-color-chip__name">${color.name}</span>
            </button>
        `).join('');

        dressPalette.querySelectorAll('[data-color-index]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const index = Number(btn.dataset.colorIndex);
                const selected = dressColors.splice(index, 1)[0];
                dressColors.unshift(selected);
                syncDressColors();
                renderDressPalette();
                schedulePreview();
            });
        });
    };

    const syncDressColors = () => {
        if (dressJsonInput) {
            dressJsonInput.value = JSON.stringify(dressColors);
        }
    };

    const syncRsvp = () => {
        if (rsvpInput) rsvpInput.value = rsvpEnabled ? '1' : '0';
        if (rsvpToggle) {
            rsvpToggle.classList.toggle('is-on', rsvpEnabled);
            rsvpToggle.setAttribute('aria-checked', String(rsvpEnabled));
        }
        const rsvpSection = document.getElementById('builder-preview-rsvp');
        if (rsvpSection) rsvpSection.classList.toggle('hidden', !rsvpEnabled);
    };

    const updateCountdown = () => {
        const target = form.event_at?.value ? new Date(form.event_at.value).getTime() : NaN;
        if (Number.isNaN(target)) return;

        const diff = Math.max(0, target - Date.now());
        const days = Math.floor(diff / 86400000);
        const hours = Math.floor((diff % 86400000) / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);

        setPreviewText('cd-days', pad2(days));
        setPreviewText('cd-hours', pad2(hours));
        setPreviewText('cd-minutes', pad2(minutes));
        setPreviewText('cd-seconds', pad2(seconds));
    };

    const updatePreview = () => {
        const state = readFormState();

        setPreviewText('groom_name', state.groom_name || 'Kuyov');
        setPreviewText('bride_name', state.bride_name || 'Kelin');
        setPreviewText('event_type', state.event_type || 'Nikoh To\'yi');

        const subtitle = [formatEventDate(state.event_at), state.event_city].filter(Boolean).join(' · ');
        setPreviewText('welcome_subtitle', subtitle);

        setPreviewText('invitation_text_1', state.invitation_text_1);
        setPreviewText('invitation_text_2', state.invitation_text_2);
        togglePreviewBlock('invitation_text_2', Boolean(state.invitation_text_2));

        setPreviewText('family_signature', state.family_signature);
        togglePreviewBlock('family_signature_wrap', Boolean(state.family_signature));

        setPreviewText('venue_name', state.venue_name);
        setPreviewText('venue_address', state.venue_address);

        renderDressPreview();
        syncRsvp();
        updateCountdown();
        updateReviewSummary();
    };

    const schedulePreview = () => {
        if (previewRaf) cancelAnimationFrame(previewRaf);
        previewRaf = requestAnimationFrame(updatePreview);
    };

    const validateStep = (step) => {
        const panel = studio.querySelector(`.builder-step[data-step="${step}"]`);
        if (!panel) return true;

        const required = [...panel.querySelectorAll('input[required], textarea[required]')];
        let valid = true;

        required.forEach((field) => {
            const ok = field.value.trim() !== '';
            field.classList.toggle('is-invalid', !ok);
            if (!ok) valid = false;
        });

        return valid;
    };

    const setStep = (step) => {
        currentStep = Math.min(Math.max(step, 1), totalSteps);

        steps.forEach((el) => {
            const active = Number(el.dataset.step) === currentStep;
            el.classList.toggle('is-active', active);
            el.hidden = !active;
        });

        stepIndicators.forEach((el) => {
            const num = Number(el.dataset.stepIndicator);
            el.classList.toggle('is-active', num === currentStep);
            el.classList.toggle('is-done', num < currentStep);
        });

        if (backBtn) backBtn.disabled = currentStep === 1;

        if (nextBtn) {
            const continueLabel = nextBtn.dataset.continueLabel || 'Davom etish';
            const saveLabel = nextBtn.dataset.saveLabel || 'Saqlash';
            nextBtn.textContent = currentStep === totalSteps ? saveLabel : continueLabel;
        }

        studio.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const updateReviewSummary = () => {
        if (!reviewList) return;

        const state = readFormState();
        const rows = [
            ['Juftlik', `${state.groom_name} & ${state.bride_name}`],
            ['Sana', `${formatEventDate(state.event_at)} · ${formatEventTime(state.event_at)}`],
            ['Joy', state.venue_name],
            ['Manzil', state.venue_address],
            ['RSVP', rsvpEnabled ? 'Yoqilgan' : 'O\'chirilgan'],
        ];

        reviewList.innerHTML = rows.map(([label, value]) => `
            <div class="builder-review__row">
                <dt>${label}</dt>
                <dd>${value || '—'}</dd>
            </div>
        `).join('');
    };

    const openCheckout = () => {
        const state = readFormState();
        const couple = document.getElementById('checkout-couple');
        const event = document.getElementById('checkout-event');

        if (couple) couple.textContent = `${state.groom_name} & ${state.bride_name}`;
        if (event) event.textContent = `${formatEventDate(state.event_at)} · ${state.venue_name}`;

        checkoutModal?.classList.add('is-open');
        checkoutModal?.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    const closeCheckout = () => {
        checkoutModal?.classList.remove('is-open');
        checkoutModal?.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    const submitForm = (publish) => {
        if (publishInput) publishInput.value = publish ? '1' : '0';
        syncDressColors();
        syncRsvp();
        form.requestSubmit();
    };

    const openPreviewSheet = () => {
        if (!previewSheet || !previewPanel || !previewSheetMount) return;
        previewSheetMount.appendChild(previewPanel);
        previewSheet.classList.add('is-open');
        previewSheet.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    const closePreviewSheet = () => {
        if (!previewSheet || !previewPanel || !previewCol) return;
        previewCol.appendChild(previewPanel);
        previewSheet.classList.remove('is-open');
        previewSheet.setAttribute('aria-hidden', 'true');
        if (!checkoutModal?.classList.contains('is-open')) {
            document.body.style.overflow = '';
        }
    };

    const syncMusicPreset = () => {
        if (!musicPreset || !musicUrlInput || !musicUrlWrap) return;

        const option = musicPreset.options[musicPreset.selectedIndex];
        const isCustom = musicPreset.value === 'custom';

        musicUrlWrap.classList.toggle('hidden', !isCustom);

        if (!isCustom && option?.dataset.url) {
            musicUrlInput.value = option.dataset.url;
        }
    };

    studio.querySelectorAll('[data-preview-input]').forEach((input) => {
        input.addEventListener('input', schedulePreview);
        input.addEventListener('change', schedulePreview);
    });

    form.event_at?.addEventListener('change', schedulePreview);

    musicPreset?.addEventListener('change', syncMusicPreset);

    rsvpToggle?.addEventListener('click', () => {
        rsvpEnabled = !rsvpEnabled;
        syncRsvp();
        schedulePreview();
    });

    backBtn?.addEventListener('click', () => setStep(currentStep - 1));

    nextBtn?.addEventListener('click', () => {
        if (!validateStep(currentStep)) return;

        if (currentStep < totalSteps) {
            setStep(currentStep + 1);
            return;
        }

        openCheckout();
    });

    previewFab?.addEventListener('click', openPreviewSheet);
    previewSheet?.querySelectorAll('[data-close-preview-sheet]').forEach((el) => {
        el.addEventListener('click', closePreviewSheet);
    });

    checkoutModal?.querySelectorAll('[data-close-checkout]').forEach((el) => {
        el.addEventListener('click', closeCheckout);
    });

    checkoutModal?.querySelectorAll('[data-checkout-action]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!validateStep(1) || !validateStep(2)) {
                closeCheckout();
                setStep(1);
                return;
            }

            submitForm(btn.dataset.checkoutAction === 'publish');
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCheckout();
            closePreviewSheet();
        }
    });

    if (musicPreset && bootstrap.music_url) {
        const matchesPreset = [...musicPreset.options].some(
            (option) => option.dataset.url && option.dataset.url === bootstrap.music_url
        );
        if (!matchesPreset) {
            musicPreset.value = 'custom';
        }
    }

    renderDressPalette();
    syncDressColors();
    syncRsvp();
    syncMusicPreset();
    setStep(1);
    schedulePreview();

    countdownTimer = window.setInterval(updateCountdown, 1000);
    window.addEventListener('beforeunload', () => clearInterval(countdownTimer));
}

document.addEventListener('DOMContentLoaded', initBuilderStudio);
