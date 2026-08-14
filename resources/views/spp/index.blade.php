@extends('layouts.app')

@section('content')
<x-page-header 
    title="SPP Siswa" 
    subtitle="Manajemen pembayaran SPP siswa"
    icon="payments"
/>

<x-card variant="default" padding="none">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-4 sm:px-6 py-4 border-b border-outline-variant">
        <form method="GET" action="{{ route('spp.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="month" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-1.5 px-2 text-label-md">
                @foreach($months as $val => $label)
                    <option value="{{ $val }}" @selected($month === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="year" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-1.5 px-2 text-label-md">
                @for($y = now()->year; $y >= 2024; $y--)
                    <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
                @endfor
            </select>
            <x-button variant="primary" type="submit" icon="search" size="sm">Tampilkan</x-button>
        </form>
        <div class="flex flex-wrap items-center gap-3 text-label-md">
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-tertiary-container inline-block"></span>
                Lunas <strong class="text-on-surface">{{ $totalLunas }}</strong>
            </span>
            <span class="w-px h-4 bg-outline-variant"></span>
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-error inline-block"></span>
                Belum <strong class="text-on-surface">{{ $totalBelum }}</strong>
            </span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Siswa</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">NIS</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Nominal</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($students as $student)
                @php $fee = $student->studentFees->first(); @endphp
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-md text-on-surface font-semibold">{{ $student->name }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $student->nis }}</td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $student->classroom?->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($fee?->is_paid)
                            <span class="text-tertiary-container text-label-md bg-tertiary-fixed/40 px-2 py-0.5 rounded flex items-center gap-1 w-fit">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span> Lunas
                            </span>
                        @else
                            <span class="text-error text-label-md bg-error-container/50 px-2 py-0.5 rounded flex items-center gap-1 w-fit">
                                <span class="material-symbols-outlined text-[16px]">cancel</span> Belum
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-md text-on-surface-variant">
                        @if($fee)
                            Rp {{ number_format($fee->amount, 0, ',', '.') }}
                        @else
                            <span class="text-on-surface-variant">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if(auth()->user()->isBendahara())
                        <div class="flex items-center justify-end gap-2">
                            @if($fee?->is_paid)
                                <form action="{{ route('spp.mark-unpaid', $fee) }}" method="POST"
                                    data-confirm="Batalkan pembayaran SPP {{ $student->name }}?"
                                    data-confirm-title="Batalkan Pembayaran"
                                    data-confirm-variant="warning"
                                    data-confirm-confirm-text="Ya, Batalkan">
                                    @csrf
                                    <input type="hidden" name="reason" value="Koreksi / pembatalan oleh bendahara">
                                    <x-button variant="outline" type="submit" icon="undo" size="sm">Batalkan</x-button>
                                </form>
                            @else
                                <x-button variant="primary" type="button" icon="payments" size="sm" data-student-id="{{ $student->id }}" data-student-name="{{ $student->name }}" onclick="showPayModal(this)">Bayar</x-button>
                            @endif
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-on-surface-variant">Tidak ada data siswa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($students->hasPages())
    <div class="p-6 border-t border-outline-variant">{{ $students->links() }}</div>
    @endif
</x-card>

{{-- Pay Modal --}}
<div id="pay-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="bg-surface-container-lowest rounded-lg shadow-[0_10px_25px_rgba(0,0,0,0.15)] w-full max-w-sm">
        <div class="flex items-center justify-between px-6 pt-6 pb-2 border-b border-outline-variant">
            <h3 class="text-headline-md font-bold text-on-surface">Catat Pembayaran SPP</h3>
            <button type="button" onclick="hidePayModal()" class="p-1 rounded-full hover:bg-surface-container-high text-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form id="pay-form" method="POST" action="{{ route('spp.mark-paid') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="student_id" id="pay-student-id">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="bg-surface-container-low rounded-lg p-4">
                <p class="text-caption text-on-surface-variant mb-1">SISWA</p>
                <p class="text-body-md text-on-surface font-semibold" id="pay-student-name"></p>
            </div>

            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">NOMINAL SPP (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-body-md text-on-surface-variant">Rp</span>
                    <input type="number" name="amount" id="pay-amount" required min="0" step="5000" value="100000"
                        class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2.5 pl-10 pr-3 text-body-md w-full">
                </div>
            </div>

            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">METODE PEMBAYARAN</label>
                <select name="method" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2.5 px-3 text-body-md w-full">
                    <option value="cash">Tunai</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="virtual_account">Virtual Account</option>
                    <option value="qris">QRIS / E-Wallet</option>
                </select>
            </div>

            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">NO. BUKTI (opsional)</label>
                <input type="text" name="reference" maxlength="100"
                    class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2.5 px-3 text-body-md w-full"
                    placeholder="Contoh: TRF/123456, VA 880...">
            </div>

            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">BUKTI PEMBAYARAN (wajib)</label>
                <input type="file" name="proof" accept="image/jpeg,image/png" required
                    class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2.5 px-3 text-body-md w-full">
                <p class="text-caption text-on-surface-variant mt-1">Foto kwitansi / bukti transfer, maksimal 2MB (JPG/PNG).</p>
            </div>

            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">CATATAN (opsional)</label>
                <input type="text" name="note" maxlength="500"
                    class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2.5 px-3 text-body-md w-full"
                    placeholder="Keterangan tambahan bila perlu">
            </div>

            <div class="flex flex-col-reverse gap-2 pt-2 border-t border-outline-variant sm:flex-row sm:justify-end">
                <button type="button" onclick="hidePayModal()" class="w-full sm:w-auto px-6 py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors">Batal</button>
                <x-button variant="primary" type="submit" icon="check" class="w-full sm:w-auto justify-center">Catat & Terbitkan Kwitansi</x-button>
            </div>
        </form>
    </div>
</div>

<script>
function showPayModal(btn) {
    document.getElementById('pay-student-id').value = btn.dataset.studentId;
    document.getElementById('pay-student-name').textContent = 'Siswa: ' + btn.dataset.studentName;
    document.getElementById('pay-modal').classList.remove('hidden');
}

function hidePayModal() {
    document.getElementById('pay-modal').classList.add('hidden');
}

document.addEventListener('click', function(e) {
    if (e.target.id === 'pay-modal') {
        hidePayModal();
    }
});
</script>
@endsection
