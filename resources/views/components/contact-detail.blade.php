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
        <div class="modal-content w-full max-w-[640px] bg-surface-container-lowest rounded-2xl shadow-2xl ring-1 ring-outline-variant dark:ring-outline dark:shadow-none dark:ring-1 dark:ring-primary/20 transform transition-all duration-200">
            {{-- Green top accent --}}
            <div class="h-1.5 bg-green-600 dark:bg-green-500 rounded-t-2xl"></div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-lg pt-lg pb-md border-b border-outline-variant dark:border-outline">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-700 dark:text-green-400">mail</span>
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
            <div class="px-lg py-lg space-y-lg max-h-[60vh] overflow-y-auto">
                {{-- Informasi --}}
                <div class="bg-surface-container-low rounded-xl p-lg border border-outline-variant dark:border-outline">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-lg gap-y-md">
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
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300 text-label-md font-semibold border border-green-300 dark:border-green-700">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    Dibalas
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 text-label-md font-semibold border border-amber-300 dark:border-amber-700">
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
                    <div class="bg-surface-container-high rounded-xl p-lg border border-outline-variant dark:border-outline">
                        <p class="text-body-md text-on-surface whitespace-pre-line leading-relaxed">
                            {{ $message?->message ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Balasan Admin --}}
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[18px] text-green-600 dark:text-green-400">reply</span>
                        <p class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Balasan Admin</p>
                    </div>
                    @if($message?->admin_reply)
                        <div class="bg-green-50 dark:bg-green-900/25 rounded-xl p-lg border border-green-200 dark:border-green-800">
                            <p class="text-body-md text-on-surface whitespace-pre-line leading-relaxed">
                                {{ $message->admin_reply }}
                            </p>
                            @if($message->replied_at)
                                <p class="text-caption text-green-700 dark:text-green-400 font-medium mt-sm">
                                    {{ $message->replied_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center gap-3 px-lg py-md rounded-xl bg-surface-container-high border border-dashed border-amber-300 dark:border-amber-700">
                            <span class="material-symbols-outlined text-[20px] text-amber-600 dark:text-amber-400">hourglass_empty</span>
                            <span class="text-body-md text-amber-800 dark:text-amber-300 font-semibold">Belum Dibalas</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-md px-lg py-md border-t border-outline-variant dark:border-outline bg-surface-container-low rounded-b-2xl">
                <button type="button"
                        onclick="window.closeModal('{{ $mid }}')"
                        class="px-lg py-2 rounded-lg border border-outline-variant dark:border-outline text-label-md font-medium text-on-surface hover:bg-surface-container-high transition-colors">
                    Tutup
                </button>
                <button type="button"
                        onclick="window.print()"
                        class="px-lg py-2 rounded-lg bg-green-600 text-label-md font-medium text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 transition-colors flex items-center gap-1">
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
