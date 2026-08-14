@extends('layouts.app')

@section('content')
<x-page-header title="Dashboard Bendahara" subtitle="Ringkasan penerimaan SPP dan kwitansi" icon="payments" />

{{-- Key Metrics --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-gutter mb-gutter">
    <div class="reveal stagger-delay bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm lift" style="--stagger: 0">
        <p class="text-caption text-on-surface-variant uppercase mb-2">Penerimaan Hari Ini</p>
        <p class="text-[28px] leading-9 font-bold font-data-table text-primary count-up" data-count="Rp {{ number_format($todayIncome, 0, ',', '.') }}">Rp 0</p>
        <p class="text-xs text-on-surface-variant mt-1 count-up" data-count="{{ $todayPayments }} kwitansi">0 kwitansi</p>
    </div>
    <div class="reveal stagger-delay bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm lift" style="--stagger: 1">
        <p class="text-caption text-on-surface-variant uppercase mb-2">Penerimaan {{ $months[$month] }}</p>
        <p class="text-[28px] leading-9 font-bold font-data-table text-primary count-up" data-count="Rp {{ number_format($monthIncome, 0, ',', '.') }}">Rp 0</p>
        <p class="text-xs text-on-surface-variant mt-1 count-up" data-count="{{ $monthPayments }} kwitansi">0 kwitansi</p>
    </div>
    <div class="reveal stagger-delay bg-primary-container text-on-primary-container rounded-xl p-6 shadow-sm" style="--stagger: 2">
        <p class="text-caption uppercase mb-2 opacity-80">Siswa Sudah Bayar</p>
        <p class="text-[28px] leading-9 font-bold font-data-table count-up" data-count="{{ number_format($paidStudents) }}">0 <span class="text-lg">/ {{ number_format($totalStudents) }}</span></p>
        <p class="text-xs mt-1 opacity-80">{{ $paidStudents > 0 ? round($paidStudents / max($totalStudents, 1) * 100) : 0 }}% lunas bulan ini</p>
    </div>
    <div class="reveal stagger-delay bg-secondary-container/40 border border-secondary/20 rounded-xl p-6 shadow-sm" style="--stagger: 3">
        <p class="text-caption text-on-surface-variant uppercase mb-2">Belum Bayar</p>
        <p class="text-[28px] leading-9 font-bold font-data-table text-on-surface count-up" data-count="{{ max($totalStudents - $paidStudents, 0) }}">0</p>
        <p class="text-xs text-on-surface-variant mt-1">siswa belum lunas bulan ini</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
    {{-- Left: Per Kelas --}}
    <div class="lg:col-span-2 flex flex-col gap-gutter">
        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-title-lg text-on-surface">Tingkat Pembayaran per Kelas</h2>
                <a href="{{ route('spp.index') }}" class="text-secondary text-label-md hover:underline">Kelola SPP</a>
            </div>
            @forelse($perClass as $row)
                <div class="mb-4 last:mb-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-body-md text-body-md font-semibold text-on-surface">{{ $row['name'] }}</span>
                        <span class="font-data-table text-data-table text-on-surface-variant">{{ $row['paid'] }}/{{ $row['total'] }}</span>
                    </div>
                    <div class="h-2.5 bg-surface-container rounded-full overflow-hidden">
                        <div class="h-full bg-tertiary-fixed-dim rounded-full transition-all"
                             style="width: {{ $row['total'] > 0 ? round($row['paid'] / $row['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-body-md text-on-surface-variant text-center py-6">Belum ada data kelas.</p>
            @endforelse
        </section>

        {{-- Recent Reversals --}}
        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                <h2 class="text-title-lg text-on-surface">Kwitansi Terbaru</h2>
                <a href="{{ route('bendahara.rekap') }}" class="text-secondary text-label-md hover:underline">Lihat Rekap</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant">
                            <th class="px-4 py-3 text-label-md text-on-surface-variant uppercase">Kwitansi</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant uppercase">Siswa</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant uppercase text-right">Nominal</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse($recentReceipts as $receipt)
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-data-table text-data-table font-semibold text-on-surface">{{ $receipt->receipt_number }}</p>
                                    <p class="text-caption text-on-surface-variant">{{ $receipt->methodLabel() }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-body-md text-body-md text-on-surface">{{ $receipt->student?->name ?? '-' }}</p>
                                    <p class="text-caption text-on-surface-variant">{{ $receipt->student?->classroom?->name ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-right font-data-table text-data-table text-on-surface">Rp {{ number_format((float) $receipt->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    @if($receipt->isReversal())
                                        <span class="text-error text-label-md bg-error-container/50 px-2 py-0.5 rounded">Batal</span>
                                    @else
                                        <span class="text-tertiary-container text-label-md bg-tertiary-fixed/40 px-2 py-0.5 rounded">Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-on-surface-variant">Belum ada kwitansi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Right: Metode Pembayaran --}}
    <div class="flex flex-col gap-gutter">
        <section class="bg-surface-container-low border border-outline-variant rounded-xl shadow-sm p-6">
            <h2 class="text-title-lg text-on-surface mb-4">Panduan Cepat</h2>
            <ul class="space-y-3">
                <li class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-lg bg-primary-container text-on-primary-container flex items-center justify-center shrink-0 material-symbols-outlined text-[18px]">receipt_long</span>
                    <div>
                        <p class="font-body-md text-body-md font-semibold text-on-surface">Catat Pembayaran</p>
                        <p class="text-caption text-on-surface-variant">Setiap bayar wajib diisi metode & upload bukti. Nomor kwitansi dibuat otomatis berurutan.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-lg bg-warning-container text-on-warning-container flex items-center justify-center shrink-0 material-symbols-outlined text-[18px]">block</span>
                    <div>
                        <p class="font-body-md text-body-md font-semibold text-on-surface">Kwitansi Tak Bisa Diubah</p>
                        <p class="text-caption text-on-surface-variant">Kesalahan dibetulkan lewat pembatalan (reversal), kwitansi asli tetap tersimpan.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0 material-symbols-outlined text-[18px]">summarize</span>
                    <div>
                        <p class="font-body-md text-body-md font-semibold text-on-surface">Rekap & Ekspor</p>
                        <p class="text-caption text-on-surface-variant">Unduh rekap bulanan CSV dari menu Rekap untuk arsip dan audit.</p>
                    </div>
                </li>
            </ul>
        </section>
    </div>
</div>
@endsection