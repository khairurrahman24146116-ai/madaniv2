@extends('layouts.app')

@section('content')
<x-page-header 
    title="SPP Siswa" 
    subtitle="Manajemen pembayaran SPP siswa"
    icon="payments"
/>

@if(session('success'))
<div class="mb-lg p-md bg-green-50 text-green-800 rounded-xl text-[14px] flex items-start gap-3 border border-green-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
    <div>{{ session('success') }}</div>
</div>
@endif

@if(session('error'))
<div class="mb-lg p-md bg-red-50 text-red-800 rounded-xl text-[14px] flex items-start gap-3 border border-red-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">error</span>
    <div>{{ session('error') }}</div>
</div>
@endif

<x-card variant="default" padding="none">
    <div class="flex items-center justify-between gap-md px-lg py-md border-b border-outline-variant">
        <form method="GET" action="{{ route('spp.index') }}" class="flex items-center gap-2">
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
        <div class="flex items-center gap-3 text-label-md">
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-green-600 inline-block"></span>
                Lunas <strong class="text-on-surface">{{ $totalLunas }}</strong>
            </span>
            <span class="w-px h-4 bg-outline-variant"></span>
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-red-600 inline-block"></span>
                Belum <strong class="text-on-surface">{{ $totalBelum }}</strong>
            </span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Siswa</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">NIS</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Nominal</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($students as $student)
                @php $fee = $student->studentFees->first(); @endphp
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold">{{ $student->name }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $student->nis }}</td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">{{ $student->classroom?->name ?? '-' }}</td>
                    <td class="px-lg py-md">
                        @if($fee?->is_paid)
                            <span class="text-green-700 text-label-md bg-green-50 px-sm py-0.5 rounded flex items-center gap-1 w-fit">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span> Lunas
                            </span>
                        @else
                            <span class="text-red-700 text-label-md bg-red-50 px-sm py-0.5 rounded flex items-center gap-1 w-fit">
                                <span class="material-symbols-outlined text-[16px]">cancel</span> Belum
                            </span>
                        @endif
                    </td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant">
                        @if($fee)
                            Rp {{ number_format($fee->amount, 0, ',', '.') }}
                        @else
                            <span class="text-on-surface-variant">-</span>
                        @endif
                    </td>
                    <td class="px-lg py-md text-right">
                        <div class="flex items-center justify-end gap-sm">
                            @if($fee?->is_paid)
                                <form action="{{ route('spp.mark-unpaid', $fee) }}" method="POST" onsubmit="return confirm('Batalkan pembayaran SPP {{ $student->name }}?')">
                                    @csrf
                                    <x-button variant="outline" type="submit" icon="undo" size="sm">Batalkan</x-button>
                                </form>
                            @else
                                <x-button variant="primary" type="button" icon="payments" size="sm" data-student-id="{{ $student->id }}" data-student-name="{{ $student->name }}" onclick="showPayModal(this)">Bayar</x-button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-xl text-on-surface-variant">Tidak ada data siswa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($students->hasPages())
    <div class="p-lg border-t border-outline-variant">{{ $students->links() }}</div>
    @endif
</x-card>

{{-- Pay Modal --}}
<div id="pay-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-sm">
        <div class="flex items-center justify-between px-lg pt-lg pb-sm border-b border-outline-variant">
            <h3 class="text-headline-md font-bold text-on-surface">Catat Pembayaran SPP</h3>
            <button type="button" onclick="hidePayModal()" class="p-1 rounded-full hover:bg-surface-container-high text-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form id="pay-form" method="POST" action="{{ route('spp.mark-paid') }}" class="p-lg space-y-md">
            @csrf
            <input type="hidden" name="student_id" id="pay-student-id">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="bg-surface-container-low rounded-lg p-md">
                <p class="text-caption text-on-surface-variant mb-xs">SISWA</p>
                <p class="text-body-md text-on-surface font-semibold" id="pay-student-name"></p>
            </div>

            <div>
                <label class="text-label-md text-on-surface-variant block mb-xs">NOMINAL SPP (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-body-md text-on-surface-variant">Rp</span>
                    <input type="number" name="amount" id="pay-amount" required min="0" step="5000" value="100000"
                        class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2.5 pl-10 pr-3 text-body-md w-full">
                </div>
            </div>

            <div class="flex gap-sm justify-end pt-sm border-t border-outline-variant">
                <button type="button" onclick="hidePayModal()" class="px-lg py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors">Batal</button>
                <x-button variant="primary" type="submit" icon="check">Konfirmasi Bayar</x-button>
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
