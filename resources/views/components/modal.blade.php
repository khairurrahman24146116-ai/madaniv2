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
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeModal('{{ $id }}')"></div>
    
    <!-- Modal Container -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div id="{{ $id }}-panel" class="w-full {{ $sizeClass }} bg-surface-container-lowest rounded-xl shadow-[0_10px_25px_rgba(0,0,0,0.15)] border border-outline-variant transform transition-all duration-200 scale-95 opacity-0 {{ $class }}">
            @if($title || $closeable)
                <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
                    @if($title)
                        <h2 id="{{ $id }}-title" class="text-headline-md font-semibold text-on-surface">{{ $title }}</h2>
                    @endif
                    @if($closeable)
                        <button type="button"
                                class="p-1.5 rounded-lg hover:bg-surface-container-high transition-colors text-on-surface-variant"
                                onclick="closeModal('{{ $id }}')"
                                aria-label="Tutup">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    @endif
                </div>
            @endif
            
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Modal helper (vanilla JS, tanpa Alpine)
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('{{ $id }}');
        if (!modal) return;

        window.openModal = window.openModal || function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('hidden');
            const panel = document.getElementById(id + '-panel');
            if (panel) {
                requestAnimationFrame(() => {
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                });
            }
            document.body.style.overflow = 'hidden';
            const focusables = el.querySelectorAll('button, input, select, textarea, a[href]');
            if (focusables.length > 0) focusables[0].focus();
        };
        
        window.closeModal = window.closeModal || function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            const panel = document.getElementById(id + '-panel');
            if (panel) {
                panel.classList.add('opacity-0', 'scale-95');
                panel.classList.remove('opacity-100', 'scale-100');
            }
            setTimeout(() => {
                el.classList.add('hidden');
                document.body.style.overflow = '';
            }, 150);
        };

        // Escape: tutup modal yang sedang terbuka
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal('{{ $id }}');
            }
        });

        // Focus trap
        modal.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab' || modal.classList.contains('hidden')) return;
            const focusables = modal.querySelectorAll('button, input, select, textarea, a[href]');
            if (focusables.length === 0) return;
            const first = focusables[0];
            const last = focusables[focusables.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        });
    });
</script>
@endpush