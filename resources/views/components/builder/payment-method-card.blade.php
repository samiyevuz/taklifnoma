@props(['id', 'name', 'value', 'label', 'hint', 'checked' => false])

<label class="payment-method-card {{ $checked ? 'is-selected' : '' }}" data-payment-card>
    <input
        type="radio"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        class="payment-method-card__input"
        @checked($checked)
    >
    <span class="payment-method-card__glow" aria-hidden="true"></span>
    <span class="payment-method-card__content">
        <span class="payment-method-card__icon">
            {{ $slot }}
        </span>
        <span class="payment-method-card__copy">
            <span class="payment-method-card__title">{{ $label }}</span>
            <span class="payment-method-card__hint">{{ $hint }}</span>
        </span>
        <span class="payment-method-card__check" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </span>
    </span>
</label>
