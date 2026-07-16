@props([
    'id' => 'modal',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl, full
    'closeable' => true,
    'class' => '',
])

@php
    $sizes = [
        'sm' => 'max-w-[400px]',
        'md' => 'max-w-[500px]',
        'lg' => 'max-w-[640px]',
        'xl' => 'max-w-[800px]',
        '2xl' => 'max-w-2xl',
        'full' => 'max-w-4xl',
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="$dispatch('close-modal', { id: '{{ $id }}' })"></div>
    
    <!-- Modal Container -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="w-full {{ $sizeClass }} bg-surface-container-lowest rounded-xl shadow-xl border border-outline-variant dark:border-outline dark:shadow-none dark:ring-1 dark:ring-primary/20 transform transition-all {{ $class }}">
            @if($title || $closeable)
                <div class="flex items-center justify-between p-md border-b border-outline-variant">
                    @if($title)
                        <h2 id="{{ $id }}-title" class="text-headline-md font-semibold text-on-surface">{{ $title }}</h2>
                    @endif
                    @if($closeable)
                        <button type="button"
                                class="p-1.5 rounded-lg hover:bg-surface-container-high transition-colors text-on-surface-variant"
                                @click="$dispatch('close-modal', { id: '{{ $id }}' })"
                                aria-label="Tutup">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    @endif
                </div>
            @endif
            
            <div class="p-md">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Alpine.js modal helper
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('{{ $id }}');
        if (!modal) return;
        
        window.openModal = window.openModal || function(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('hidden');
        };
        
        window.closeModal = window.closeModal || function(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        };
        
        modal.addEventListener('close-modal', (e) => {
            if (e.detail.id === '{{ $id }}') {
                modal.classList.add('hidden');
            }
        });
        
        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
@endpush