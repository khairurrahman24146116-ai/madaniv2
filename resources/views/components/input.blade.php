@props([
    'type' => 'text', // text, email, password, number, date, select, textarea
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'hint' => null,
    'class' => '',
    'options' => [], // for select
    'rows' => 3, // for textarea
])

@php
    $inputClasses = 'w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface placeholder:text-on-surface-variant transition-colors';
    $focusClasses = 'focus:ring-2 focus:ring-primary/25 focus:border-primary outline-none';
    $disabledClasses = 'disabled:bg-surface-container-high disabled:cursor-not-allowed disabled:opacity-75';
    $errorClasses = $error ? 'border-error focus:ring-error/25 focus:border-error' : '';
    $sizes = 'px-4 py-2.5 text-body-md';
    
    $labelClasses = 'block text-label-md font-medium text-on-surface-variant mb-1.5';
    $errorTextClasses = 'mt-1.5 text-caption text-error';
    $hintTextClasses = 'mt-1.5 text-caption text-on-surface-variant';
@endphp

<div class="{{ $class }}">
    @if($label)
        <label for="{{ $name }}" class="{{ $labelClasses }}">
            {{ $label }} @if($required) <span class="text-error">*</span> @endif
        </label>
    @endif
    
    @if($type === 'select')
        <select name="{{ $name }}"
                id="{{ $name }}"
                class="{{ $inputClasses }} {{ $focusClasses }} {{ $disabledClasses }} {{ $errorClasses }} {{ $sizes }}"
                @if($disabled) disabled @endif
                @if($required) required @endif>
            @if($placeholder)
                <option value="" disabled selected>{{ $placeholder }}</option>
            @endif
            @foreach($options as $value => $label)
                <option value="{{ $value }}" @selected(old($name, $value) == $value)>{{ $label }}</option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea name="{{ $name }}"
                  id="{{ $name }}"
                  rows="{{ $rows }}"
                  class="{{ $inputClasses }} {{ $focusClasses }} {{ $disabledClasses }} {{ $errorClasses }} {{ $sizes }} resize-y"
                  @if($disabled) disabled @endif
                  @if($readonly) readonly @endif
                  @if($required) required @endif
                  placeholder="{{ $placeholder }}">{{ old($name, $value) }}</textarea>
    @else
        <input type="{{ $type }}"
               name="{{ $name }}"
               id="{{ $name }}"
               value="{{ old($name, $value) }}"
               class="{{ $inputClasses }} {{ $focusClasses }} {{ $disabledClasses }} {{ $errorClasses }} {{ $sizes }}"
               @if($disabled) disabled @endif
               @if($readonly) readonly @endif
               @if($required) required @endif
               @if($placeholder) placeholder="{{ $placeholder }}" @endif>
    @endif
    
    @if($error)
        <p class="{{ $errorTextClasses }}">{{ $error }}</p>
    @elseif($hint)
        <p class="{{ $hintTextClasses }}">{{ $hint }}</p>
    @endif
</div>