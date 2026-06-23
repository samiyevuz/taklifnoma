# i18n Audit Plan — Taklifnoma

**Date:** 2026-06-23  
**Locales:** `uz` (default), `ru`, `en`  
**Scope:** Blade views, `lang/`, Livewire/controllers, `builder.js`, `invitation.js`, admin, invitation templates

---

## Summary

| Category | Count | Priority | Status |
|----------|------:|----------|--------|
| Missing lang keys (referenced in views) | 6 entries | P0 | Fixed |
| `builder.js` hardcoded strings | ~52 | P0 | Fixed via `window.builderI18n` |
| `nikoh-premium` partial translations | 8 | P0 | Fixed |
| Landing testimonials (inline Uzbek) | ~22 | P1 | Fixed → `landing.testimonials*` |
| Landing features demo panel | ~14 | P1 | Fixed → `builder.*` keys |
| Admin blade leftovers | ~12 | P1 | Fixed → `admin.*` keys |
| Layout meta / aria labels | ~10 | P2 | Fixed |
| RSVP API + validation messages | ~8 | P1 | Fixed → `invitation.*` |
| `PlanEntitlements` feature labels | ~12 | P1 | Fixed → `builder.plan_features.*` |
| `InvitationDefaults` dress/music | ~25 | P1 | Fixed → `builder.defaults.*` |
| `TemplateVariantCatalog` subtitles | ~90 | P2 | Fixed → `variants.php` |
| Telegram bot copy | ~35 | P3 | Deferred (admin-only notifications) |

**Grand total addressed:** ~280 user-facing strings  
**Lang files:** 8 domains → 9 (+ `variants.php`)

---

## Phase 1 — Findings

### Lang file parity

All 8 original domain files (`account`, `admin`, `auth`, `builder`, `invitation`, `landing`, `nav`, `share`) had **identical key structures** across uz/ru/en (~537 keys each).

**Gaps found in code (not in lang files):**

| Key | Location |
|-----|----------|
| `nav.loading` | `layouts/premium.blade.php` |
| `invitation.rsvp_desc` | `components/builder/preview-panel.blade.php` |

### Hardcoded strings by area

1. **Builder JS** — No i18n bridge; all UI text Uzbek-only (months, review labels, plan notices, payment errors).
2. **nikoh-premium** — 8 aria-labels and map link in Uzbek only; ~32 keys already use `__('invitation.*')`.
3. **Landing** — `testimonials.blade.php` fully inline; `features.blade.php` demo RSVP panel hardcoded.
4. **Admin** — `invitations/show`, `dashboard`, `users/show` partial Uzbek.
5. **Support PHP** — `TemplateVariantCatalog`, `InvitationDefaults`, `PlanEntitlements`, `RsvpController`.

### Architecture notes

- **Good pattern:** `layouts/invitation.blade.php` → `window.invitationI18n` (replicated for builder).
- **FAQ content:** DB + `SiteContent` with lang fallbacks.
- **No Livewire** — all i18n is Blade + controllers + Support + Vite JS.

---

## Phase 2 — Fix plan

### P0 — Visible bugs

1. Add `nav.loading`, `invitation.rsvp_desc` (all locales).
2. Inject `window.builderI18n` in `layouts/builder.blade.php`; refactor `builder.js`.
3. Fix `nikoh-premium` aria-labels → `invitation.*`.

### P1 — Customer-facing surfaces

4. Move testimonials to `landing.php`; update blade to `__()`.
5. Wire features demo panel to existing `builder.rsvp_live_*` / `builder.stats_*`.
6. Internationalize `RsvpController`, `StoreRsvpRequest`.
7. `PlanEntitlements::featureLabels()` → `builder.plan_features.*`.
8. `InvitationDefaults` dress colors & music presets → `builder.defaults.*`.
9. Add `lang/*/variants.php`; resolve subtitles in `TemplateVariantCatalog::tiers()`.

### P2 — Admin & meta

10. Admin invitation show, dashboard table headers, users show.
11. Layout meta descriptions, navbar/footer aria labels.
12. Fix `lang/uz/admin.php` English leak (`nav.dashboard`, `panel`).

### P3 — Deferred

- `TelegramMessageFormatter` (~35 strings) — operator-facing, low guest impact.
- `welcome.blade.php`, `ui-kit-preview` — unused/dev pages.

---

## Phase 3 — Verification

```bash
npm run build
php artisan test
git commit -m "Complete missing translations for uz, ru, and en locales."
git push origin main
```

---

## Implementation order (completed)

1. Missing keys + audit doc  
2. Builder i18n bridge + JS refactor  
3. nikoh-premium + invitation keys  
4. Landing testimonials + features demo  
5. RSVP API + validation  
6. Support classes (PlanEntitlements, InvitationDefaults, TemplateVariantCatalog)  
7. Admin blades + layout meta  
8. Build, test, deploy
