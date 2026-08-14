@extends('layouts.app')

@section('content')
<x-page-header title="Rekap Keuangan SPP" subtitle="Ringkasan penerimaan per bulan dengan rincian kwitansi" icon="summarize" />

{{-- Filter + Export --}}
<form action="{{ route('bendahara.rekap') }}" method="GET" class="flex flex-wrap gap-4 mb-6">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">BULAN</label>
        <select name="month" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
            @foreach($months as $val => $label)
                <option value="{{ $val }}" @selected($month === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-1">TAHUN</label>
        <select name="year" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
            @for($y = now()->year; $y >= 2024; $y--)
                <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="self-end flex gap-2">
        <x-button variant="primary" type="submit" icon="search">Tampilkan</x-button>
        <x-button variant="secondary" type="button" icon="download" href="{{ route('bendahara.rekap.export', ['month' => $month, 'year' => $year]) }}">Export CSV</x-button>
    </div>
</form>

{{-- Ringkasan --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-gutter">
    <div class="bg-primary-container text-on-primary-container rounded-xl p-6 shadow-sm">
        <p class="text-caption uppercase mb-2 opacity-80">Total Penerimaan {{ $months[$month] }} {{ $year }}</p>
        <p class="text-[28px] leading-9 font-bold font-data-table">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        <p class="text-xs mt-1 opacity-80">{{ $income->count() }} kwitansi lunas</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
        <p class="text-caption text-on-surface-variant uppercase mb-2">Dibataalkan (Reversal)</p>
        <p class="text-[28px] leading-9 font-bold font-data-table text-error">Rp {{ number_format((float) $reversed->sum('amount'), 0, ',', '.') }}</p>
        <p class="text-xs text-on-surface-variant mt-1">{{ $reversed->count() }} entri reversal</p>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
        <p class="text-caption text-on-surface-variant uppercase mb-2">Per Metode</p>
        @forelse($byMethod as $method => $total)
            <div class="flex justify-between items-center py-1">
                <span class="text-body-md text-on-surface">{{ \App\Models\PaymentReceipt::METHODS_LABELS[$method] ?? $method }}</span>
                <span class="font-data-table text-data-table font-semibold text-on-surface">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        @empty
            <p class="text-caption text-on-surface-variant">Belum ada data.</p>
        @endforelse
    </div>
</div>

{{-- Detail Kwitansi --}}
<x-card variant="default">
    <div class="p-6 border-b border-outline-variant bg-surface-container-low">
        <h3 class="text-headline-md text-on-surface">Rincian Kwitansi {{ $months[$month] }} {{ $year }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">No. Kwitansi</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Siswa</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Metode</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase text-right">Nominal</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Status</th>
                    <th class="px-6 py-4 text-label-md text-on-surface-variant uppercase">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($receipts as $receipt)
                    <tr class="hover:bg-surface-container-low transition-colors {{ $receipt->isReversal() ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4 font-data-table text-data-table font-semibold text-on-surface">{{ $receipt->receipt_number }}</td>
                        <td class="px-6 py-4 text-body-md text-on-surface">{{ $receipt->student?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $receipt->student?->classroom?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $receipt->methodLabel() }}</td>
                        <td class="px-6 py-4 text-right font-data-table text-data-table text-on-surface">Rp {{ number_format((float) $receipt->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($receipt->isReversal())
                                <span class="text-error text-label-md bg-error-container/50 px-2 py-0.5 rounded">Batal</span>
                            @else
                                <span class="text-tertiary-container text-label-md bg-tertiary-fixed/40 px-2 py-0.5 rounded">Lunas</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-on-surface-variant">Belum ada kwitansi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection