@props(['size' => 'md'])

@php
$sizes = [
    'sm' => 'h-9 w-9',
    'md' => 'h-10 w-10',
    'lg' => 'w-24 h-24',
];
@endphp

<img src="{{ asset('images/logo-yayasan.png') }}"
     alt="Logo Yayasan Dayah Madani Al-Aziziyah"
     {{ $attributes->merge(['class' => ($sizes[$size] ?? $sizes['md']).' object-contain']) }}>