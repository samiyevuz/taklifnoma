/**
 * Taklifnoma — Interactive Invitation Builder Studio
 * Dynamic event profiles · Live preview · Stepper · Checkout modal
 */

const ELITE_EASE = 'cubic-bezier(0.16, 1, 0.3, 1)';
const UZ_MONTHS = [
    '', 'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun',
    'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr',
];

const LAYOUT_MODES = {
    couple: 'couple',
    couple_bride_first: 'couple_bride_first',
    child: 'child',
    celebrant: 'celebrant',
    graduation: 'graduation',
    general: 'general',
};

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

const HERO_SCALE_CLASSES = ['is-compact', 'is-tight', 'is-micro'];

function heroScaleTier(length) {
    if (length > 50) return 'is-micro';
    if (length > 35) return 'is-tight';
    if (length > 20) return 'is-compact';
    return '';
}

function applyHeroTypography(elements, text) {
    const tier = heroScaleTier((text || '').length);

    elements.filter(Boolean).forEach((el) => {
        el.classList.remove(...HERO_SCALE_CLASSES);
        if (tier) el.classList.add(tier);
    });
}

function createProfileEngine(schema) {
    const layout = schema?.layout || LAYOUT_MODES.couple;
    const fields = Array.isArray(schema?.fields) ? schema.fields : [];
    const preview = schema?.preview || {};
    const fieldByKey = Object.fromEntries(fields.map((field) => [field.key, field]));
    const fieldByRole = Object.fromEntries(fields.map((field) => [field.preview, field]));

    const getValue = (form, key) => {
        const input = form.querySelector(`[data-profile-key="${key}"]`);
        return input?.value?.trim() || '';
    };

    const readProfileState = (form) => {
        const values = {};
        fields.forEach((field) => {
            values[field.key] = getValue(form, field.key);
        });
        return values;
    };

    const resolveHero = (values) => {
        const placeholders = preview.placeholders || {};
        const primaryField = fieldByRole.primary;
        const secondaryField = fieldByRole.secondary;

        const primary = values[primaryField?.key] || placeholders.primary || '';
        const secondary = values[secondaryField?.key] || placeholders.secondary || '';

        if (layout === LAYOUT_MODES.couple_bride_first) {
            return {
                primary: values.bride_name || placeholders.primary || 'Kelin',
                secondary: values.groom_name || placeholders.secondary || 'Kuyov',
            };
        }

        if (layout === LAYOUT_MODES.couple) {
            return {
                primary: values.groom_name || placeholders.primary || 'Kuyov',
                secondary: values.bride_name || placeholders.secondary || 'Kelin',
            };
        }

        if (layout === LAYOUT_MODES.child) {
            return {
                primary: values.child_name || placeholders.primary || 'Bola ismi',
                secondary: values.hosts || '',
                tagline: preview.tagline || '',
            };
        }

        if (layout === LAYOUT_MODES.celebrant) {
            return {
                primary: values.celebrant_name || placeholders.primary || 'Ism',
                secondary: values.milestone || placeholders.secondary || '',
                tagline: preview.tagline || '',
            };
        }

        if (layout === LAYOUT_MODES.graduation) {
            return {
                primary: values.school_name || placeholders.primary || 'Maktab nomi',
                secondary: values.class_name || placeholders.secondary || 'Sinf / guruh',
                tagline: preview.tagline || '',
            };
        }

        return {
            primary: values.primary_name || placeholders.primary || 'Mezbon',
            secondary: values.secondary_name || '',
        };
    };

    const buildDisplayTitle = (values) => {
        const hero = resolveHero(values);

        if (layout === LAYOUT_MODES.child) {
            return hero.primary;
        }

        if (layout === LAYOUT_MODES.celebrant || layout === LAYOUT_MODES.graduation) {
            return [hero.primary, hero.secondary].filter(Boolean).join(' · ');
        }

        if (layout === LAYOUT_MODES.general) {
            return [hero.primary, hero.secondary].filter(Boolean).join(' & ');
        }

        return [hero.primary, hero.secondary].filter(Boolean).join(' & ');
    };

    const buildReviewRows = (state, rsvpEnabled) => {
        const hero = resolveHero(state.profile);
        const rows = [
            [preview.review_label || 'Asosiy', buildDisplayTitle(state.profile)],
            ['Sana', `${formatEventDate(state.event_at)} · ${formatEventTime(state.event_at)}`],
            ['Joy', state.venue_name],
            ['Manzil', state.venue_address],
            ['RSVP', rsvpEnabled ? 'Yoqilgan' : 'O\'chirilgan'],
        ];

        if (layout === LAYOUT_MODES.child && hero.secondary) {
            rows.splice(1, 0, ['Taklif etuvchilar', hero.secondary]);
        }

        return rows;
    };

    return {
        layout,
        fields,
        preview,
        fieldByKey,
        readProfileState,
        resolveHero,
        buildDisplayTitle,
        buildReviewRows,
    };
}

function initBuilderStudio() {
    const studio = document.getElementById('builder-studio');
    if (!studio) return;

    const bootstrap = JSON.parse(studio.dataset.bootstrap || '{}');
    const form = document.getElementById('builder-form');
    if (!form) return;

    const profileEngine = createProfileEngine(bootstrap.field_schema || {});

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
    const heroBlocks = [...studio.querySelectorAll('[data-preview-layout]')];

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

    const applyPreviewLayout = () => {
        const layout = profileEngine.layout;

        heroBlocks.forEach((block) => {
            const layouts = (block.dataset.previewLayout || '').split(/\s+/).filter(Boolean);
            const active = layouts.includes(layout);
            block.classList.toggle('hidden', !active);
        });

        const showConnector = Boolean(profileEngine.preview.show_connector)
            && [LAYOUT_MODES.couple, LAYOUT_MODES.couple_bride_first].includes(layout);

        togglePreviewBlock('hero_connector', showConnector);

        if (layout === LAYOUT_MODES.general) {
            const values = profileEngine.readProfileState(form);
            togglePreviewBlock('hero_connector', Boolean(values.secondary_name));
        }
    };

    const readFormState = () => ({
        profile: profileEngine.readProfileState(form),
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
        const hero = profileEngine.resolveHero(state.profile);
        const layout = profileEngine.layout;

        applyPreviewLayout();

        setPreviewText('event_type', state.event_type || bootstrap.template_title || '');

        const subtitle = [formatEventDate(state.event_at), state.event_city].filter(Boolean).join(' · ');
        setPreviewText('welcome_subtitle', subtitle);

        if (layout === LAYOUT_MODES.child || layout === LAYOUT_MODES.celebrant) {
            setPreviewText('hero_primary_single', hero.primary);
            setPreviewText('hero_tagline', hero.tagline || '');

            if (layout === LAYOUT_MODES.child) {
                setPreviewText('hero_hosts', hero.secondary ? `${hero.secondary} nomidan` : '');
            } else {
                setPreviewText('hero_hosts', hero.secondary || '');
            }

            togglePreviewBlock('hero_hosts_wrap', Boolean(hero.secondary));
        } else if (layout === LAYOUT_MODES.graduation) {
            setPreviewText('hero_primary_stacked', hero.primary);
            setPreviewText('hero_secondary_stacked', hero.secondary);
            setPreviewText('hero_tagline_graduation', hero.tagline || '');
        } else {
            setPreviewText('hero_primary', hero.primary);
            setPreviewText('hero_secondary', hero.secondary);

            if (layout === LAYOUT_MODES.general) {
                togglePreviewBlock('hero_secondary', Boolean(hero.secondary));
                togglePreviewBlock('hero_connector', Boolean(hero.secondary));
            }
        }

        setPreviewText('invitation_text_1', state.invitation_text_1);
        setPreviewText('invitation_text_2', state.invitation_text_2);
        togglePreviewBlock('invitation_text_2', Boolean(state.invitation_text_2));

        setPreviewText('family_signature', state.family_signature);
        togglePreviewBlock('family_signature_wrap', Boolean(state.family_signature));

        setPreviewText('venue_name', state.venue_name);
        setPreviewText('venue_address', state.venue_address);

        const heroNameEls = [...studio.querySelectorAll('.builder-preview-page .inv-welcome__names')];
        const hostsEls = previewMap.hero_hosts || [];

        if (layout === LAYOUT_MODES.child || layout === LAYOUT_MODES.celebrant) {
            applyHeroTypography(heroNameEls, hero.primary);
            applyHeroTypography(hostsEls, hero.secondary);
        } else if (layout === LAYOUT_MODES.graduation) {
            applyHeroTypography(heroNameEls, `${hero.primary} ${hero.secondary}`.trim());
        } else {
            const combined = [hero.primary, hero.secondary].filter(Boolean).join(' & ');
            applyHeroTypography(heroNameEls, combined);
        }

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

        const required = [
            ...panel.querySelectorAll('input[required], textarea[required]'),
            ...panel.querySelectorAll('[data-profile-required]'),
        ];

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
        const rows = profileEngine.buildReviewRows(state, rsvpEnabled);

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

        if (couple) couple.textContent = profileEngine.buildDisplayTitle(state.profile);
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
