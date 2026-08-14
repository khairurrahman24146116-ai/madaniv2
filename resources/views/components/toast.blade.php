@php
    $flashes = [];
    foreach (['success' => 'success', 'error' => 'error', 'info' => 'info', 'warning' => 'warning', 'status' => 'info'] as $key => $type) {
        if (session($key)) {
            $flashes[] = ['type' => $type, 'message' => session($key)];
        }
    }
@endphp

<div id="toast-container" class="fixed top-4 right-4 z-[100] flex flex-col gap-2 w-[calc(100%-2rem)] max-w-sm pointer-events-none" aria-live="polite"></div>

@if (count($flashes))
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashes = @json($flashes);
        flashes.forEach(f => window.showToast(f.type, f.message));
    });
</script>
@endpush
@endif

@once
@push('scripts')
<script>
    window.showToast = function (type, message) {
        const icons = { success: 'check_circle', error: 'error', warning: 'warning', info: 'info' };
        const chips = {
            success: 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
            error: 'bg-error/10 text-error',
            warning: 'bg-warning/10 text-warning',
            info: 'bg-primary/10 text-primary',
        };
        const container = document.getElementById('toast-container');
        if (!container) return;

        const el = document.createElement('div');
        el.className = 'pointer-events-auto flex items-start gap-3 p-4 rounded-xl border border-outline-variant bg-surface-container-lowest shadow-lg transform transition-all duration-300 translate-x-6 opacity-0';
        el.setAttribute('role', 'status');

        const chip = document.createElement('span');
        chip.className = 'w-9 h-9 shrink-0 rounded-full flex items-center justify-center material-symbols-outlined text-[20px] ' + (chips[type] || chips.info);
        chip.textContent = icons[type] || icons.info;

        const body = document.createElement('div');
        body.className = 'flex-1 py-1 text-body-md text-on-surface';
        body.textContent = message;

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'shrink-0 p-1 rounded-lg hover:bg-surface-container transition-colors text-on-surface-variant';
        closeBtn.setAttribute('aria-label', 'Tutup notifikasi');
        const closeIcon = document.createElement('span');
        closeIcon.className = 'material-symbols-outlined text-[18px]';
        closeIcon.textContent = 'close';
        closeBtn.appendChild(closeIcon);

        el.appendChild(chip);
        el.appendChild(body);
        el.appendChild(closeBtn);
        container.appendChild(el);

        requestAnimationFrame(() => requestAnimationFrame(() => {
            el.classList.remove('translate-x-6', 'opacity-0');
        }));

        const dismiss = () => {
            el.classList.add('translate-x-6', 'opacity-0');
            setTimeout(() => el.remove(), 300);
        };
        closeBtn.addEventListener('click', dismiss);
        setTimeout(dismiss, 4500);
    };
</script>
@endpush
@endonce
