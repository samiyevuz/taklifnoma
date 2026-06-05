@props(['schema'])

@php
    $fields = $schema['fields'] ?? [];
    $layout = $schema['layout'] ?? 'couple';
    $isWideSingle = count($fields) === 1;
@endphp

<div
    id="builder-profile-fields"
    class="builder-profile-fields {{ $isWideSingle ? 'builder-profile-fields--single' : 'builder-grid-2' }}"
    data-profile-layout="{{ $layout }}"
>
    @foreach ($fields as $field)
        <div
            class="builder-field builder-field--float {{ $field['required'] ? 'builder-field--required' : '' }}"
            data-profile-field="{{ $field['key'] }}"
            data-preview-role="{{ $field['preview'] }}"
        >
            <input
                type="text"
                id="profile_{{ $field['key'] }}"
                name="{{ $field['name'] }}"
                value="{{ old('profile.'.$field['key'], $field['value']) }}"
                placeholder=" "
                @if ($field['required']) required data-profile-required @endif
                data-preview-input
                data-profile-key="{{ $field['key'] }}"
                data-preview-role="{{ $field['preview'] }}"
            >
            <label for="profile_{{ $field['key'] }}">{{ $field['label'] }}</label>
        </div>
    @endforeach
</div>
