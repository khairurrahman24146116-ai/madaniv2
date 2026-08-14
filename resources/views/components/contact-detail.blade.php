@props(['message' => null])

@php
    $mid = Str::slug('contact-detail-' . ($message->id ?? '0'));
@endphp

<div id="{{ $mid }}"
     class="fixed inset-0 z-50 hidden"
     role="dialog"
     aria-modal="true"
     aria-labelledby="{{ $mid }}-title">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
         onclick="window.closeModal('{{ $mid }}')"></div>

    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-[640px] bg-surface-container-lowest rounded-xl shadow-lg ring-1 ring-outline-variant transform transition-all duration-200">
            {{-- Top accent --}}
            <div class="h-1.5 bg-primary rounded-t-xl"></div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-outline-variant">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container">mail</span>
                    </div>
                    <h2 id="{{ $mid }}-title" class="text-headline-md font-bold text-on-surface">Detail Pesan</h2>
                </div>
                <button type="button"
                        onclick="window.closeModal('{{ $mid }}')"
                        class="p-1.5 rounded-lg hover:bg-surface-container-high transition-colors text-on-surface-variant"
                        aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6 space-y-6 max-h-[60vh] overflow-y-auto">
                {{-- Informasi --}}
                <div class="bg-surface-container-low rounded-lg p-6 border border-outline-variant">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-caption text-on-surface-variant uppercase tracking-wider mb-1">Pengirim</p>
                            <p class="text-body-md text-on-surface font-semibold">{{ $message?->user?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-on-surface-variant uppercase tracking-wider mb-1">Subjek</p>
                            <p class="text-body-md text-on-surface font-semibold">{{ $message?->subject ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-on-surface-variant uppercase tracking-wider mb-1">Tanggal</p>
                            <p class="text-body-md text-on-surface font-semibold">
                                {{ $message?->created_at ? $message->created_at->format('d/m/Y H:i') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-caption text-on-surface-variant uppercase tracking-wider mb-1">Status</p>
                            @if($message?->admin_reply)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant text-label-md font-semibold border border-tertiary/30">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    Dibalas
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-warning-container text-on-warning-container text-label-md font-semibold border border-warning/30">
                                    <span class="material-symbols-outlined text-[16px]">pending</span>
                                    Menunggu
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Isi Pesan --}}
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[18px] text-primary">article</span>
                        <p class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Isi Pesan</p>
                    </div>
                    <div class="bg-surface-container-high rounded-lg p-6 border border-outline-variant">
                        <p class="text-body-md text-on-surface whitespace-pre-line leading-relaxed">
                            {{ $message?->message ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Balasan Admin --}}
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[18px] text-tertiary">reply</span>
                        <p class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Balasan Admin</p>
                    </div>
                    @if($message?->admin_reply)
                        <div class="bg-tertiary-fixed/40 rounded-lg p-6 border border-tertiary/30">
                            <p class="text-body-md text-on-surface whitespace-pre-line leading-relaxed">
                                {{ $message->admin_reply }}
                            </p>
                            @if($message->replied_at)
                                <p class="text-caption text-tertiary-container font-medium mt-2">
                                    {{ $message->replied_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center gap-3 px-6 py-4 rounded-lg bg-surface-container-high border border-dashed border-warning/50">
                            <span class="material-symbols-outlined text-[20px] text-warning">hourglass_empty</span>
                            <span class="text-body-md text-on-warning-container font-semibold">Belum Dibalas</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-4 px-6 py-4 border-t border-outline-variant bg-surface-container-low rounded-b-xl">
                <button type="button"
                        onclick="window.closeModal('{{ $mid }}')"
                        class="px-6 py-2 rounded-lg border border-outline-variant text-label-md font-medium text-on-surface hover:bg-surface-container-high transition-colors">
                    Tutup
                </button>
                <button type="button"
                        onclick="window.print()"
                        class="px-6 py-2 rounded-lg bg-primary text-label-md font-medium text-on-primary hover:bg-primary/90 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #{{ $mid }} .modal-content {
        animation: ocFadeIn 0.2s ease-out;
    }
    @keyframes ocFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>

@push('scripts')
<script>
    (function() {
        if (document.getElementById('{{ $mid }}')) {
            if (!window._contactModalIds) window._contactModalIds = [];
            window._contactModalIds.push('{{ $mid }}');
        }
        if (!window._contactEscSetup) {
            window._contactEscSetup = true;
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && window._contactModalIds) {
                    for (var i = 0; i < window._contactModalIds.length; i++) {
                        var el = document.getElementById(window._contactModalIds[i]);
                        if (el && !el.classList.contains('hidden')) {
                            el.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    }
                }
            });
        }
        if (!window.openModal) {
            window.openModal = function(id) {
                var el = document.getElementById(id);
                if (el) { el.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
            };
        }
        if (!window.closeModal) {
            window.closeModal = function(id) {
                var el = document.getElementById(id);
                if (el) { el.classList.add('hidden'); document.body.style.overflow = ''; }
            };
        }
    })();
</script>
@endpush
