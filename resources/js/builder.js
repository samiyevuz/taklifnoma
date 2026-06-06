/**
 * Taklifnoma — Interactive Invitation Builder Studio
 * Dynamic event profiles · Live preview · Stepper · Checkout modal
 */

import { initRsvpLivePanels } from './rsvp-live-panel';

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

function formatVariantPrice(amount, currency = "so'm") {
    const value = Number(amount);
    if (!Number.isFinite(value)) return '';
    return `${value.toLocaleString('uz-UZ').replace(/,/g, ' ')} ${currency}`;
}

const VARIANT_THEME_CLASSES = ['inv-theme--classic', 'inv-theme--premium', 'inv-theme--luxury', 'inv-theme--royal'];
const VARIANT_ANIM_CLASSES = ['inv-anim--basic', 'inv-anim--enhanced', 'inv-anim--cinematic', 'inv-anim--vip'];
const PHONE_TIER_CLASSES = ['builder-phone--tier-1', 'builder-phone--tier-2', 'builder-phone--tier-3', 'builder-phone--tier-4'];

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
    let activeDressIndex = 0;
    let rsvpEnabled = Boolean(bootstrap.rsvp_enabled ?? true);
    let coverPreviewUrl = bootstrap.cover_image_url || null;
    let previewMapInstance = null;
    let previewMapMarker = null;
    let pickerMapInstance = null;
    let pickerMapMarker = null;
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
    const variants = Array.isArray(bootstrap.template_variants) ? bootstrap.template_variants : [];
    let variantIndex = Math.max(0, variants.findIndex((variant) => variant.id === bootstrap.template_variant));
    if (variantIndex < 0) variantIndex = 0;

    const variantPrev = document.getElementById('builder-variant-prev');
    const variantNext = document.getElementById('builder-variant-next');
    const variantDots = document.getElementById('builder-variant-dots');
    const variantTitle = document.getElementById('builder-variant-title');
    const variantSubtitle = document.getElementById('builder-variant-subtitle');
    const variantPrice = document.getElementById('builder-variant-price');
    const variantBadge = document.getElementById('builder-variant-badge');
    const variantCarousel = document.getElementById('builder-variant-carousel');
    const templateVariantInput = document.getElementById('template_variant');
    const templateBladeInput = document.getElementById('template_blade');
    const checkoutPrice = document.getElementById('checkout-price');
    const checkoutTemplate = document.getElementById('checkout-template');
    const slugInput = document.getElementById('slug');
    const customDomainSubInput = document.getElementById('custom_domain_subdomain');
    const customDomainHidden = document.getElementById('custom_domain');
    const customDomainPrefixEl = document.getElementById('custom_domain_prefix');
    const customDomainSuffixEl = document.getElementById('custom_domain_suffix');
    let slugManuallyEdited = Boolean(slugInput?.value?.trim());
    const previewPage = document.getElementById('builder-preview');
    const previewPhone = document.getElementById('builder-phone');
    const previewPhoneScreen = document.getElementById('builder-phone-screen');
    const previewCover = document.getElementById('builder-preview-cover');
    const previewParticles = document.getElementById('builder-preview-particles');
    const previewTierRibbon = document.getElementById('builder-preview-tier-ribbon');
    const variantFeatures = document.getElementById('builder-variant-features');
    let previewRevealObserver = null;
    let previewParallaxBound = false;

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
        map_lat: form.map_lat?.value || '',
        map_lng: form.map_lng?.value || '',
        invitation_text_1: form.invitation_text_1?.value?.trim() || '',
        invitation_text_2: form.invitation_text_2?.value?.trim() || '',
        family_signature: form.family_signature?.value?.trim() || '',
    });

    const activeDressColor = () => dressColors[activeDressIndex] || dressColors[0] || null;

    const selectDressColor = (index) => {
        if (!dressColors[index]) return;
        activeDressIndex = index;
        syncDressColors();
        renderDressPalette();
        renderDressPreview();
        updateDressNote();
        updateReviewSummary();
    };

    const updateDressNote = () => {
        const noteEl = document.getElementById('builder-preview-dress-note');
        const color = activeDressColor();
        if (noteEl) {
            noteEl.textContent = color?.note || '';
        }
    };

    const renderDressPreview = () => {
        const grid = document.getElementById('builder-preview-dress');
        if (!grid) return;

        grid.innerHTML = dressColors.map((color, index) => `
            <button
                type="button"
                class="inv-dress-swatch ${index === activeDressIndex ? 'is-active' : ''}"
                role="listitem"
                data-preview-dress-index="${index}"
                aria-label="${color.name}"
                aria-pressed="${index === activeDressIndex ? 'true' : 'false'}"
            >
                <span class="inv-dress-swatch__circle" style="background-color:${color.hex}"></span>
                <span class="inv-dress-swatch__label">${color.name}</span>
            </button>
        `).join('');

        grid.querySelectorAll('[data-preview-dress-index]').forEach((btn) => {
            btn.addEventListener('click', () => {
                selectDressColor(Number(btn.dataset.previewDressIndex));
            });
        });

        updateDressNote();
    };

    const renderDressPalette = () => {
        if (!dressPalette) return;

        dressPalette.innerHTML = dressColors.map((color, index) => `
            <button
                type="button"
                class="builder-color-chip ${index === activeDressIndex ? 'is-active' : ''}"
                data-color-index="${index}"
                role="listitem"
                aria-label="${color.name}"
                aria-pressed="${index === activeDressIndex ? 'true' : 'false'}"
            >
                <span class="builder-color-chip__dot" style="background:${color.hex}"></span>
                <span class="builder-color-chip__name">${color.name}</span>
            </button>
        `).join('');

        dressPalette.querySelectorAll('[data-color-index]').forEach((btn) => {
            btn.addEventListener('click', () => {
                selectDressColor(Number(btn.dataset.colorIndex));
            });
        });
    };

    const syncDressColors = () => {
        if (dressJsonInput) {
            const ordered = [
                dressColors[activeDressIndex],
                ...dressColors.filter((_, index) => index !== activeDressIndex),
            ].filter(Boolean);

            dressJsonInput.value = JSON.stringify(ordered);
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
        updatePreviewMap(state.map_lat, state.map_lng);
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

        if (currentStep === 3 && pickerMapInstance) {
            window.setTimeout(() => pickerMapInstance.invalidateSize(), 350);
        }
    };

    const updateReviewSummary = () => {
        if (!reviewList) return;

        const state = readFormState();
        const rows = profileEngine.buildReviewRows(state, rsvpEnabled);

        const dress = activeDressColor();
        const lat = form.map_lat?.value;
        const lng = form.map_lng?.value;

        if (dress) {
            rows.splice(4, 0, ['Dress code', `${dress.name} — ${dress.note || ''}`]);
        }

        if (lat && lng) {
            rows.push(['Xarita', `${Number(lat).toFixed(5)}, ${Number(lng).toFixed(5)}`]);
        }

        const siteHost = bootstrap.slug_host || bootstrap.payments?.site_host || 'taklifnoma.net';
        const slugValue = slugInput?.value?.trim();
        if (slugValue) {
            rows.push(['Havola', `${siteHost}/l/${slugValue}`]);
        }

        syncCustomDomain();
        if (customDomainHidden?.value) {
            rows.push(['Maxsus domen', customDomainHidden.value]);
        }

        reviewList.innerHTML = rows.map(([label, value]) => `
            <div class="builder-review__row">
                <dt>${label}</dt>
                <dd>${value || '—'}</dd>
            </div>
        `).join('');
    };

    const slugifyPreview = (value) => value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '') || 'taklifnoma';

    const syncCustomDomain = () => {
        if (!customDomainSubInput || !customDomainHidden) return;

        const prefix = (customDomainPrefixEl?.textContent || '').trim();
        const suffix = (customDomainSuffixEl?.textContent || '').trim();
        const sub = customDomainSubInput.value
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9-]+/g, '-')
            .replace(/^-+|-+$/g, '');

        if (customDomainSubInput.value !== sub) {
            customDomainSubInput.value = sub;
        }

        customDomainHidden.value = sub ? `${prefix}${sub}${suffix}` : '';
    };

    const suggestSlugFromProfile = () => {
        if (!slugInput || slugManuallyEdited) return;

        const state = readFormState();
        const title = profileEngine.buildDisplayTitle(state.profile);
        slugInput.value = slugifyPreview(title);
    };

    const prepareFormForSubmit = () => {
        suggestSlugFromProfile();
        syncDressColors();
        syncRsvp();
        syncMusicPreset();
        syncCustomDomain();
    };

    const renderPreviewParticles = (animationTier) => {
        if (!previewParticles) return;

        const count = animationTier === 'vip' ? 14 : animationTier === 'cinematic' ? 9 : 0;
        previewParticles.innerHTML = count
            ? Array.from({ length: count }, (_, index) => (
                `<span class="builder-preview-particle" style="--p-i:${index};--p-x:${8 + (index * 6.5) % 84}%;--p-y:${6 + (index * 11) % 88}%;--p-d:${0.4 + (index % 5) * 0.35}s"></span>`
            )).join('')
            : '';
    };

    const replayWelcomeEntrance = () => {
        const welcome = previewPage?.querySelector('.inv-welcome');
        if (!welcome) return;

        welcome.classList.remove('is-entering');
        void welcome.offsetWidth;
        welcome.classList.add('is-entering');
    };

    const resetPreviewScrollReveals = () => {
        if (!previewPage) return;

        previewPage.querySelectorAll('.inv-reveal').forEach((element) => {
            element.classList.remove('is-visible');
        });
    };

    const initPreviewScrollReveal = () => {
        if (!previewPage || !previewPhoneScreen) return;

        if (previewRevealObserver) {
            previewRevealObserver.disconnect();
        }

        resetPreviewScrollReveals();

        previewRevealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            },
            {
                root: previewPhoneScreen,
                threshold: 0.18,
                rootMargin: '0px 0px -12px 0px',
            }
        );

        previewPage.querySelectorAll('.inv-reveal').forEach((element) => {
            previewRevealObserver.observe(element);
        });
    };

    const syncPreviewParallax = () => {
        if (!previewPhoneScreen || !previewCover || !previewPage) return;

        const animationTier = VARIANT_ANIM_CLASSES.find((cls) => previewPage.classList.contains(cls))
            ?.replace('inv-anim--', '') || 'enhanced';

        if (!['cinematic', 'vip'].includes(animationTier)) {
            previewCover.style.transform = '';
            return;
        }

        const scrollTop = previewPhoneScreen.scrollTop;
        const scale = animationTier === 'vip' ? 1.14 : 1.08;
        previewCover.style.transform = `translate3d(0, ${scrollTop * 0.28}px, 0) scale(${scale})`;
    };

    const bindPreviewParallax = () => {
        if (!previewPhoneScreen || previewParallaxBound) return;

        previewPhoneScreen.addEventListener('scroll', syncPreviewParallax, { passive: true });
        previewParallaxBound = true;
    };

    const buildPlanNotice = (entitlements) => {
        const limit = entitlements.guest_limit === null || entitlements.guest_limit === undefined
            ? 'cheksiz'
            : entitlements.guest_limit;

        switch (entitlements.tier) {
            case 'classic':
                return `Classic tarif: ${limit} mehmon. Fon musiqasi va maxsus havola yo'q.`;
            case 'luxury':
                return `Luxury tarif: ${limit} mehmongacha. Kinematik animatsiyalar va musiqa mavjud.`;
            case 'royal':
                return 'Royal VIP: cheksiz mehmon, maxsus domen va VIP effektlar.';
            default:
                return `Premium tarif: ${limit} mehmongacha. Musiqa va maxsus havola mavjud.`;
        }
    };

    const syncPlanEntitlements = (entitlements = {}) => {
        const slugWrap = document.getElementById('builder-slug-wrap');
        const customDomainWrap = document.getElementById('builder-custom-domain-wrap');
        const planNotice = document.getElementById('builder-plan-notice');
        const musicPresetField = musicPreset?.closest('.builder-field');
        const allowsMusic = Boolean(entitlements.music_enabled);

        if (slugInput) {
            slugInput.disabled = !entitlements.custom_slug;
        }
        slugWrap?.classList.toggle('is-locked', !entitlements.custom_slug);

        customDomainWrap?.classList.toggle('hidden', !entitlements.custom_domain);
        customDomainWrap?.classList.toggle('is-locked', !entitlements.custom_domain);

        if (customDomainSubInput) {
            customDomainSubInput.disabled = !entitlements.custom_domain;
        }

        musicPresetField?.classList.toggle('hidden', !allowsMusic);
        musicUrlWrap?.classList.toggle('hidden', !allowsMusic || musicPreset?.value === 'upload');
        musicFileWrap?.classList.toggle('hidden', !allowsMusic || musicPreset?.value !== 'upload');

        if (!allowsMusic) {
            if (musicUrlInput) musicUrlInput.value = '';
            if (musicPreset) musicPreset.value = musicPreset.options[0]?.value || '';
        } else if (musicUrlInput && !musicUrlInput.value.trim()) {
            syncMusicPreset();
        }

        if (planNotice) {
            planNotice.textContent = buildPlanNotice(entitlements);
        }
    };

    const applyVariant = (index, { replay = true } = {}) => {
        if (!variants.length) return;

        const safeIndex = ((index % variants.length) + variants.length) % variants.length;
        variantIndex = safeIndex;
        const variant = variants[safeIndex];
        const priceLabel = variant.price || formatVariantPrice(variant.price_amount, bootstrap.currency);
        const animationTier = variant.animation || 'enhanced';
        const tierLevel = Number(variant.tier_level) || 2;
        const coverUrl = variant.cover_url || coverPreviewUrl || '';
        const features = Array.isArray(variant.features) ? variant.features : [];

        if (templateVariantInput) templateVariantInput.value = variant.id || '';
        if (templateBladeInput) templateBladeInput.value = variant.blade || '';
        if (variantTitle) variantTitle.textContent = variant.title || '';
        if (variantSubtitle) variantSubtitle.textContent = variant.subtitle || '';
        if (variantPrice) variantPrice.textContent = priceLabel;
        if (variantBadge) {
            const badge = variant.badge || '';
            variantBadge.textContent = badge;
            variantBadge.classList.toggle('hidden', !badge);
        }
        if (variantFeatures) {
            variantFeatures.innerHTML = features.map((feature) => (
                `<span class="builder-variant-feature">${feature}</span>`
            )).join('');
        }
        if (checkoutPrice) checkoutPrice.textContent = priceLabel;
        if (checkoutTemplate) checkoutTemplate.textContent = variant.title || bootstrap.template_title || '';

        if (previewPage) {
            previewPage.classList.remove(...VARIANT_THEME_CLASSES, ...VARIANT_ANIM_CLASSES);
            previewPage.classList.add(`inv-theme--${variant.theme || 'premium'}`);
            previewPage.classList.add(`inv-anim--${animationTier}`);
        }

        if (previewPhone) {
            previewPhone.classList.remove(...PHONE_TIER_CLASSES);
            previewPhone.classList.add(`builder-phone--tier-${tierLevel}`);
        }

        if (previewCover) {
            previewCover.style.backgroundImage = coverUrl ? `url("${coverUrl}")` : '';
            previewCover.style.backgroundPosition = variant.cover_focus || 'center 40%';
        }

        renderPreviewParticles(animationTier);

        if (previewTierRibbon) {
            const ribbonLabel = animationTier === 'vip'
                ? 'VIP'
                : animationTier === 'cinematic'
                    ? 'LUXURY'
                    : '';
            previewTierRibbon.textContent = ribbonLabel;
            previewTierRibbon.classList.toggle('hidden', !ribbonLabel);
        }

        variantDots?.querySelectorAll('[data-variant-dot]').forEach((dot, dotIndex) => {
            dot.classList.toggle('is-active', dotIndex === safeIndex);
            dot.setAttribute('aria-selected', dotIndex === safeIndex ? 'true' : 'false');
        });

        const showNav = variants.length > 1;
        variantPrev?.toggleAttribute('hidden', !showNav);
        variantNext?.toggleAttribute('hidden', !showNav);

        syncPlanEntitlements(variant.entitlements || {});

        if (replay) {
            if (previewPhoneScreen) {
                previewPhoneScreen.scrollTop = 0;
            }
            resetPreviewScrollReveals();
            initPreviewScrollReveal();
            replayWelcomeEntrance();
            syncPreviewParallax();
        }
    };

    const initVariantCarousel = () => {
        if (!variants.length) {
            variantCarousel?.classList.add('is-single');
            return;
        }

        if (variantDots) {
            variantDots.innerHTML = variants.map((variant, index) => `
                <button
                    type="button"
                    class="builder-variant-dot${index === variantIndex ? ' is-active' : ''}"
                    data-variant-dot
                    data-variant-index="${index}"
                    role="tab"
                    aria-selected="${index === variantIndex ? 'true' : 'false'}"
                    aria-label="${variant.title || `Variant ${index + 1}`}"
                ></button>
            `).join('');

            variantDots.querySelectorAll('[data-variant-dot]').forEach((dot) => {
                dot.addEventListener('click', () => {
                    applyVariant(Number(dot.dataset.variantIndex));
                });
            });
        }

        variantPrev?.addEventListener('click', () => applyVariant(variantIndex - 1));
        variantNext?.addEventListener('click', () => applyVariant(variantIndex + 1));
        applyVariant(variantIndex);
        bindPreviewParallax();
    };

    const openCheckout = () => {
        const state = readFormState();
        const couple = document.getElementById('checkout-couple');
        const event = document.getElementById('checkout-event');
        const url = document.getElementById('checkout-url');
        const alert = document.getElementById('checkout-alert');
        const title = profileEngine.buildDisplayTitle(state.profile);
        const slugPreview = slugInput?.value?.trim() || slugifyPreview(title);
        const siteHost = bootstrap.slug_host || bootstrap.payments?.site_host || 'taklifnoma.net';

        syncCustomDomain();
        const publicUrl = customDomainHidden?.value
            ? customDomainHidden.value
            : `${siteHost}/l/${slugPreview}`;

        if (couple) couple.textContent = title;
        if (event) event.textContent = `${formatEventDate(state.event_at)} · ${state.venue_name}`;
        if (url) url.textContent = publicUrl;
        applyVariant(variantIndex);
        if (alert) {
            alert.textContent = '';
            alert.classList.add('hidden');
            alert.classList.remove('is-success');
        }

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
        prepareFormForSubmit();
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

    const musicFileWrap = document.getElementById('music-file-wrap');

    const syncMusicPreset = () => {
        if (!musicPreset || !musicUrlInput || !musicUrlWrap) return;

        const option = musicPreset.options[musicPreset.selectedIndex];
        const isCustom = musicPreset.value === 'custom';
        const isUpload = musicPreset.value === 'upload';

        musicUrlWrap.classList.toggle('hidden', !isCustom);
        musicFileWrap?.classList.toggle('hidden', !isUpload);

        if (!isCustom && !isUpload && option?.dataset.url) {
            musicUrlInput.value = option.dataset.url;
        } else if (!isCustom && !isUpload && !musicUrlInput.value.trim() && bootstrap.default_music_url) {
            musicUrlInput.value = bootstrap.default_music_url;
        }
    };

    const parseCoordinate = (value) => {
        const num = Number(value);
        return Number.isFinite(num) ? num : null;
    };

    const syncMapInputs = (lat, lng) => {
        if (form.map_lat) form.map_lat.value = lat ?? '';
        if (form.map_lng) form.map_lng.value = lng ?? '';
        schedulePreview();
    };

    const initPickerMap = () => {
        const container = document.getElementById('builder-map-picker');
        if (!container || typeof window.L === 'undefined') return;

        const lat = parseCoordinate(form.map_lat?.value) ?? 41.311081;
        const lng = parseCoordinate(form.map_lng?.value) ?? 69.240562;

        pickerMapInstance = window.L.map(container, {
            zoomControl: true,
            scrollWheelZoom: true,
        }).setView([lat, lng], 15);

        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(pickerMapInstance);

        pickerMapMarker = window.L.marker([lat, lng], { draggable: true }).addTo(pickerMapInstance);

        const onMove = (position) => {
            syncMapInputs(position.lat.toFixed(7), position.lng.toFixed(7));
            if (previewMapMarker && previewMapInstance) {
                previewMapMarker.setLatLng(position);
                previewMapInstance.setView(position, previewMapInstance.getZoom());
            }
        };

        pickerMapMarker.on('dragend', () => onMove(pickerMapMarker.getLatLng()));
        pickerMapInstance.on('click', (event) => {
            pickerMapMarker.setLatLng(event.latlng);
            onMove(event.latlng);
        });

        window.setTimeout(() => pickerMapInstance.invalidateSize(), 250);
    };

    const updatePreviewMap = (latValue, lngValue) => {
        const container = document.getElementById('builder-preview-map');
        if (!container || typeof window.L === 'undefined') return;

        const lat = parseCoordinate(latValue);
        const lng = parseCoordinate(lngValue);
        const hasCoords = lat !== null && lng !== null;

        container.classList.toggle('hidden', !hasCoords);
        container.setAttribute('aria-hidden', hasCoords ? 'false' : 'true');

        if (!hasCoords) return;

        const position = [lat, lng];

        if (!previewMapInstance) {
            previewMapInstance = window.L.map(container, {
                zoomControl: false,
                dragging: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                boxZoom: false,
                keyboard: false,
                tap: false,
            }).setView(position, 15);

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OSM',
            }).addTo(previewMapInstance);

            previewMapMarker = window.L.marker(position).addTo(previewMapInstance);
            window.setTimeout(() => previewMapInstance.invalidateSize(), 200);
            return;
        }

        previewMapMarker.setLatLng(position);
        previewMapInstance.setView(position, previewMapInstance.getZoom());
        window.setTimeout(() => previewMapInstance.invalidateSize(), 100);
    };

    const geocodeAddress = async () => {
        const venue = form.venue_name?.value?.trim() || '';
        const address = form.venue_address?.value?.trim() || '';
        const city = form.event_city?.value?.trim() || '';
        const query = [venue, address, city, 'Uzbekistan'].filter(Boolean).join(', ');

        if (!query) return;

        const button = document.getElementById('map-geocode-btn');
        if (button) button.disabled = true;

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`,
                { headers: { Accept: 'application/json' } }
            );

            if (!response.ok) throw new Error('Geocode failed');

            const results = await response.json();
            const hit = results?.[0];

            if (!hit) throw new Error('Manzil topilmadi');

            const lat = Number(hit.lat);
            const lng = Number(hit.lon);

            syncMapInputs(lat.toFixed(7), lng.toFixed(7));

            if (pickerMapInstance && pickerMapMarker) {
                pickerMapMarker.setLatLng([lat, lng]);
                pickerMapInstance.setView([lat, lng], 16);
            }
        } catch {
            window.alert('Manzil bo\'yicha joy topilmadi. Xaritadan qo\'lda belgilang.');
        } finally {
            if (button) button.disabled = false;
        }
    };

    const initMediaUploads = () => {
        const coverInput = document.getElementById('cover_image');
        const coverPreview = document.getElementById('cover-upload-preview');
        const coverFilename = document.getElementById('cover-filename');
        const musicInput = document.getElementById('music_file');
        const musicFilename = document.getElementById('music-filename');

        coverInput?.addEventListener('change', () => {
            const file = coverInput.files?.[0];
            if (!file) return;

            if (coverFilename) {
                coverFilename.textContent = file.name;
                coverFilename.classList.remove('hidden');
            }

            const reader = new FileReader();
            reader.onload = () => {
                coverPreviewUrl = reader.result;
                if (coverPreview) {
                    coverPreview.src = reader.result;
                    coverPreview.classList.remove('hidden');
                }
                if (previewCover) {
                    previewCover.style.backgroundImage = `url("${reader.result}")`;
                }
            };
            reader.readAsDataURL(file);
        });

        musicInput?.addEventListener('change', () => {
            const file = musicInput.files?.[0];
            if (!file || !musicFilename) return;
            musicFilename.textContent = file.name;
            musicFilename.classList.remove('hidden');
        });
    };

    slugInput?.addEventListener('input', () => {
        slugManuallyEdited = true;
        schedulePreview();
    });

    customDomainSubInput?.addEventListener('input', () => {
        syncCustomDomain();
        schedulePreview();
    });

    studio.querySelectorAll('[data-preview-input]').forEach((input) => {
        input.addEventListener('input', () => {
            schedulePreview();
            if (input.hasAttribute('data-profile-field') || input.name?.startsWith('profile')) {
                suggestSlugFromProfile();
            }
        });
        input.addEventListener('change', schedulePreview);
    });

    form.event_at?.addEventListener('change', schedulePreview);

    musicPreset?.addEventListener('change', syncMusicPreset);

    document.getElementById('map-geocode-btn')?.addEventListener('click', geocodeAddress);

    form.map_lat?.addEventListener('change', () => {
        const lat = parseCoordinate(form.map_lat.value);
        const lng = parseCoordinate(form.map_lng?.value);
        if (lat !== null && lng !== null && pickerMapMarker && pickerMapInstance) {
            pickerMapMarker.setLatLng([lat, lng]);
            pickerMapInstance.setView([lat, lng], pickerMapInstance.getZoom());
        }
        schedulePreview();
    });

    form.map_lng?.addEventListener('change', () => {
        const lat = parseCoordinate(form.map_lat?.value);
        const lng = parseCoordinate(form.map_lng.value);
        if (lat !== null && lng !== null && pickerMapMarker && pickerMapInstance) {
            pickerMapMarker.setLatLng([lat, lng]);
            pickerMapInstance.setView([lat, lng], pickerMapInstance.getZoom());
        }
        schedulePreview();
    });

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

    const initPaymentMethodCards = () => {
        checkoutModal?.querySelectorAll('[data-payment-card]').forEach((card) => {
            const input = card.querySelector('.payment-method-card__input');

            const sync = () => {
                checkoutModal.querySelectorAll('[data-payment-card]').forEach((item) => {
                    item.classList.toggle('is-selected', item.querySelector('.payment-method-card__input')?.checked);
                });
            };

            input?.addEventListener('change', sync);
            card.addEventListener('click', () => {
                if (!input) return;
                input.checked = true;
                sync();
            });

            sync();
        });
    };

    const initiatePayment = async () => {
        if (!validateStep(1) || !validateStep(2)) {
            closeCheckout();
            setStep(1);
            return;
        }

        const payBtn = document.getElementById('checkout-pay-btn');
        const alert = document.getElementById('checkout-alert');
        const isComplimentary = Boolean(bootstrap.payments?.complimentary);
        const provider = isComplimentary
            ? 'complimentary'
            : checkoutModal?.querySelector('input[name="payment_provider"]:checked')?.value;

        if (!isComplimentary && !provider) {
            if (alert) {
                alert.textContent = 'To\'lov usulini tanlang.';
                alert.classList.remove('hidden', 'is-success');
            }
            return;
        }

        if (!bootstrap.payments?.generate_url) {
            if (alert) {
                alert.textContent = 'To\'lov tizimi sozlanmagan.';
                alert.classList.remove('hidden', 'is-success');
            }
            return;
        }

        prepareFormForSubmit();

        const formData = new FormData(form);
        formData.append('payment_provider', provider);

        if (payBtn) payBtn.disabled = true;

        try {
            const response = await fetch(bootstrap.payments.generate_url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const payload = await response.json();

            if (!response.ok || !payload?.success) {
                const message = payload?.message
                    || Object.values(payload?.errors || {}).flat().join(' ')
                    || 'To\'lovni boshlashda xatolik yuz berdi.';
                throw new Error(message);
            }

            const redirectUrl = payload?.data?.redirect_url;

            if (!redirectUrl) {
                throw new Error('To\'lov havolasi yaratilmadi.');
            }

            if (payload?.data?.provider === 'complimentary') {
                window.location.href = redirectUrl;
                return;
            }

            if (payload?.data?.invitation_id) {
                let hidden = form.querySelector('#invitation_id');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'invitation_id';
                    hidden.id = 'invitation_id';
                    form.appendChild(hidden);
                }
                hidden.value = payload.data.invitation_id;

                if (payload?.data?.form_action) {
                    form.action = payload.data.form_action;
                    let method = form.querySelector('input[name="_method"]');
                    if (!method) {
                        method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        form.appendChild(method);
                    }
                    method.value = 'PUT';
                }
            }

            window.location.href = redirectUrl;
        } catch (error) {
            if (alert) {
                alert.textContent = error.message || 'To\'lovni boshlashda xatolik yuz berdi.';
                alert.classList.remove('hidden', 'is-success');
            }
            if (payBtn) payBtn.disabled = false;
        }
    };

    document.getElementById('checkout-pay-btn')?.addEventListener('click', initiatePayment);

    checkoutModal?.querySelectorAll('[data-checkout-action]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!validateStep(1) || !validateStep(2)) {
                closeCheckout();
                setStep(1);
                return;
            }

            submitForm(false);
        });
    });

    initPaymentMethodCards();

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCheckout();
            closePreviewSheet();
        }
    });

    if (musicPreset) {
        if (bootstrap.music_url) {
            const matchesPreset = [...musicPreset.options].some(
                (option) => option.dataset.url && option.dataset.url === bootstrap.music_url
            );
            if (!matchesPreset) {
                musicPreset.value = 'custom';
            }
        } else if (bootstrap.default_music_url) {
            musicUrlInput.value = bootstrap.default_music_url;
        }
    }

    syncCustomDomain();
    suggestSlugFromProfile();

    initVariantCarousel();
    renderDressPalette();
    syncDressColors();
    syncRsvp();
    syncMusicPreset();
    initMediaUploads();
    initPickerMap();
    setStep(1);
    schedulePreview();

    countdownTimer = window.setInterval(updateCountdown, 1000);
    window.addEventListener('beforeunload', () => clearInterval(countdownTimer));

    initRsvpLivePanels();
}

document.addEventListener('DOMContentLoaded', initBuilderStudio);
