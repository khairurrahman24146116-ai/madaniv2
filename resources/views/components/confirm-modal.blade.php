<div id="confirm-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title" aria-describedby="confirm-modal-message">
    <div id="confirm-modal-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 sm:p-6">
        <div id="confirm-modal-panel" class="w-full max-w-md bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0_10px_25px_rgba(0,0,0,0.15)] transform transition-all duration-200 scale-95 opacity-0">
            <div class="p-6 sm:p-8">
                <div class="flex items-start gap-4 sm:gap-6">
                    <div id="confirm-modal-icon-chip" class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary/10 text-primary">
                        <span id="confirm-modal-icon-glyph" class="material-symbols-outlined text-[28px] leading-none select-none">info</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 id="confirm-modal-title" class="text-headline-md font-semibold text-on-surface">Konfirmasi</h3>
                        <p id="confirm-modal-message" class="text-body-md text-on-surface-variant mt-2 break-words leading-relaxed"></p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 p-6 sm:flex-row sm:justify-end sm:p-8 sm:pt-0">
                <button id="confirm-modal-cancel" type="button" class="h-12 px-8 rounded-full border border-outline-variant text-label-md text-on-surface-variant hover:bg-surface-container transition-colors w-full sm:w-auto">Batal</button>
                <button id="confirm-modal-ok" type="button" class="h-12 px-8 rounded-full bg-primary text-on-primary text-label-md hover:bg-primary/90 transition-colors shadow-sm w-full sm:w-auto">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    (function () {
        var modal = document.getElementById('confirm-modal');
        if (!modal) return;

        var panel = document.getElementById('confirm-modal-panel');
        var titleEl = document.getElementById('confirm-modal-title');
        var messageEl = document.getElementById('confirm-modal-message');
        var chipEl = document.getElementById('confirm-modal-icon-chip');
        var glyphEl = document.getElementById('confirm-modal-icon-glyph');
        var okBtn = document.getElementById('confirm-modal-ok');
        var cancelBtn = document.getElementById('confirm-modal-cancel');
        var backdrop = document.getElementById('confirm-modal-backdrop');

        var chipBase = 'flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full ';
        var variants = {
            danger: { icon: 'warning', chip: 'bg-error/10 text-error', ok: 'bg-error text-on-error hover:bg-error/90' },
            warning: { icon: 'warning', chip: 'bg-warning/10 text-warning', ok: 'bg-primary text-on-primary hover:bg-primary/90' },
            info: { icon: 'info', chip: 'bg-primary/10 text-primary', ok: 'bg-primary text-on-primary hover:bg-primary/90' },
        };

        var pendingForm = null;
        var pendingTarget = null;
        var bypassClick = false;

        function lockScroll(lock) {
            document.body.style.overflow = lock ? 'hidden' : '';
        }

        function openConfirm(state, onConfirm) {
            var v = variants[state.variant] || variants.warning;
            glyphEl.textContent = state.icon || v.icon;
            chipEl.className = chipBase + v.chip;
            titleEl.textContent = state.title || 'Konfirmasi';
            messageEl.textContent = state.message || '';
            okBtn.textContent = state.confirmText || 'Ya, Lanjutkan';
            cancelBtn.textContent = state.cancelText || 'Batal';
            okBtn.className = 'h-12 px-8 rounded-full text-label-md transition-colors shadow-sm w-full sm:w-auto ' + v.ok;

            modal.classList.remove('hidden');
            requestAnimationFrame(function () {
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            });
            lockScroll(true);

            okBtn.onclick = function () {
                closeConfirm();
                if (onConfirm) {
                    onConfirm();
                } else if (pendingForm) {
                    pendingForm.submit();
                } else if (pendingTarget) {
                    bypassClick = true;
                    pendingTarget.click();
                }
            };
            cancelBtn.focus();
        }

        function closeConfirm() {
            panel.classList.add('opacity-0', 'scale-95');
            panel.classList.remove('opacity-100', 'scale-100');
            setTimeout(function () {
                modal.classList.add('hidden');
                lockScroll(false);
            }, 150);
            pendingForm = null;
            pendingTarget = null;
        }

        cancelBtn.addEventListener('click', closeConfirm);
        backdrop.addEventListener('click', closeConfirm);

        document.addEventListener('keydown', function (e) {
            if (modal.classList.contains('hidden')) return;
            if (e.key === 'Escape') {
                closeConfirm();
                return;
            }
            if (e.key === 'Tab') {
                var focusables = modal.querySelectorAll('button');
                if (focusables.length === 0) return;
                var first = focusables[0];
                var last = focusables[focusables.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
            e.preventDefault();
            pendingForm = form;
            pendingTarget = null;
            openConfirm({
                title: form.getAttribute('data-confirm-title') || 'Konfirmasi',
                message: form.getAttribute('data-confirm'),
                variant: form.getAttribute('data-confirm-variant') || 'warning',
                confirmText: form.getAttribute('data-confirm-confirm-text') || 'Ya, Lanjutkan',
                cancelText: form.getAttribute('data-confirm-cancel-text') || 'Batal',
            });
        });

        document.addEventListener('click', function (e) {
            if (bypassClick) {
                bypassClick = false;
                return;
            }
            var el = e.target.closest('[data-confirm]');
            if (!el || el instanceof HTMLFormElement) return;
            e.preventDefault();
            pendingForm = null;
            pendingTarget = el;
            openConfirm({
                title: el.getAttribute('data-confirm-title') || 'Konfirmasi',
                message: el.getAttribute('data-confirm'),
                variant: el.getAttribute('data-confirm-variant') || 'warning',
                confirmText: el.getAttribute('data-confirm-confirm-text') || 'Ya, Lanjutkan',
                cancelText: el.getAttribute('data-confirm-cancel-text') || 'Batal',
            });
        });

        window.openConfirmModal = openConfirm;
        window.closeConfirmModal = closeConfirm;
    })();
</script>
@endpush
@endonce
