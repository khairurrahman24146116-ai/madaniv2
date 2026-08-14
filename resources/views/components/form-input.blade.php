@props([
    'type' => 'text', // text, email, password, number, tel, url, search, date, time, datetime-local, month, week, select, textarea, checkbox, radio, file
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'hint' => null,
    'class' => '',
    'options' => [], // for select
    'multiple' => false, // for select
    'rows' => 3, // for textarea
    'min' => null,
    'max' => null,
    'step' => null,
    'autocomplete' => null,
])

@php
    $id = $id ?? $name ?? 'input-' . uniqid();
    $inputId = $id;
    $labelId = $id . '-label';
    $errorId = $id . '-error';
    $hintId = $id . '-hint';
    
    $describedBy = [];
    if ($error) $describedBy[] = $errorId;
    if ($hint) $describedBy[] = $hintId;
    $ariaDescribedBy = $describedBy ? 'aria-describedby="' . implode(' ', $describedBy) . '"' : '';
@endphp

<div class="w-full {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" id="{{ $labelId }}" class="block text-label-md text-on-surface-variant mb-1">
            {{ $label }}
            @if($required)
                <span class="text-error ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif
    
    @if($type === 'select')
        <select name="{{ $name }}"
                id="{{ $inputId }}"
                class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed {{ $error ? 'border-error focus:ring-error/20' : '' }}"
                @if($disabled) disabled @endif
                @if($required) required @endif
                @if($multiple) multiple @endif
                {{ $ariaDescribedBy }}>
            @if($placeholder)
                <option value="" disabled selected>{{ $placeholder }}</option>
            @endif
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string)old($name, $value) === (string)$optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea name="{{ $name }}"
                  id="{{ $inputId }}"
                  rows="{{ $rows }}"
                  class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed resize-y {{ $error ? 'border-error focus:ring-error/20' : '' }}"
                  @if($disabled) disabled @endif
                  @if($required) required @endif
                  @if($placeholder) placeholder="{{ $placeholder }}" @endif
                  {{ $ariaDescribedBy }}>{{ old($name, $value) }}</textarea>
    @elseif($type === 'checkbox')
        <div class="flex items-start gap-3">
            <input type="checkbox"
                   name="{{ $name }}"
                   id="{{ $inputId }}"
                   value="1"
                   class="mt-0.5 w-4 h-4 rounded border-outline-variant text-primary focus:ring-2 focus:ring-primary/20 disabled:opacity-50 disabled:cursor-not-allowed {{ $error ? 'border-error' : '' }}"
                   @if($disabled) disabled @endif
                   @if($required) required @endif
                   @if(old($name, $value)) checked @endif
                   {{ $ariaDescribedBy }}>
            <label for="{{ $inputId }}" class="text-body-md text-on-surface cursor-pointer">
                {{ $label ?? $slot }}
            </label>
        </div>
    @elseif($type === 'radio')
        <div class="flex items-center gap-3">
            <input type="radio"
                   name="{{ $name }}"
                   id="{{ $inputId }}"
                   value="{{ $value }}"
                   class="w-4 h-4 border-outline-variant text-primary focus:ring-2 focus:ring-primary/20 disabled:opacity-50 disabled:cursor-not-allowed {{ $error ? 'border-error' : '' }}"
                   @if($disabled) disabled @endif
                   @if($required) required @endif
                   @if(old($name) == $value || $value === old($name)) checked @endif
                   {{ $ariaDescribedBy }}>
            <label for="{{ $inputId }}" class="text-body-md text-on-surface cursor-pointer">
                {{ $label ?? $slot }}
            </label>
        </div>
    @elseif($type === 'file')
        <input type="file"
               name="{{ $name }}"
               id="{{ $inputId }}"
               class="w-full text-body-md text-on-surface-variant file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-label-md file:font-semibold file:bg-primary-container file:text-on-primary-container hover:file:bg-primary/80 transition-colors disabled:opacity-50 disabled:cursor-not-allowed {{ $error ? 'border-error' : '' }}"
               @if($disabled) disabled @endif
               @if($required) required @endif
               @if($multiple) multiple @endif
               {{ $ariaDescribedBy }}>
    @else
        <input type="{{ $type }}"
               name="{{ $name }}"
               id="{{ $inputId }}"
               value="{{ old($name, $value) }}"
               placeholder="{{ $placeholder }}"
               class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed {{ $error ? 'border-error focus:ring-error/20' : '' }}"
               @if($disabled) disabled @endif
               @if($readonly) readonly @endif
               @if($required) required @endif
               @if($min !== null) min="{{ $min }}" @endif
               @if($max !== null) max="{{ $max }}" @endif
               @if($step !== null) step="{{ $step }}" @endif
               @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
               {{ $ariaDescribedBy }}>
    @endif
    
    @if($error)
        <p id="{{ $errorId }}" class="mt-1.5 text-caption text-error flex items-center gap-1" role="alert">
            <span class="material-symbols-outlined text-[14px]">error_outline</span>
            {{ $error }}
        </p>
    @elseif($hint)
        <p id="{{ $hintId }}" class="mt-1.5 text-caption text-on-surface-variant flex items-center gap-1">
            {{ $hint }}
        </p>
    @endif
</div>