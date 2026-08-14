@extends('layouts.app')

@section('content')
@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $student = $user->isWaliMurid()
        ? \App\Models\Student::where('user_id', $user->id)->with(['classroom', 'studentFees'])->first()
        : \App\Models\Student::first();

    $paidFees = $student?->studentFees()->where('is_paid', true)->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
    $currentFee = $student?->studentFees()->where('month', now()->month)->where('year', now()->year)->first();
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp

<x-page-header title="Pembayaran SPP" icon="payments">
    <p class="font-body-md text-on-surface-variant">Pantau tagihan & riwayat pembayaran SPP <span class="font-semibold text-primary">{{ $student->name ?? '-' }}</span></p>
</x-page-header>

{{-- Bento Grid (desktop: 7/5 split) --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
    {{-- Left Column: Primary Focus --}}
    <div class="lg:col-span-7 flex flex-col gap-gutter">
        {{-- Student Profile Summary Card --}}
        <section class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant flex flex-wrap items-center gap-x-6 gap-y-4 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-primary/5 rounded-bl-full pointer-events-none"></div>
            @if ($student && $student->photo_path)
                <img class="w-16 h-16 rounded-full object-cover border-2 border-surface-variant relative z-10"
                     src="{{ asset('storage/'.$student->photo_path) }}" alt="{{ $student->name }}">
            @else
                <div class="w-16 h-16 rounded-full border-2 border-surface-variant bg-primary-container text-on-primary-container flex items-center justify-center relative z-10">
                    <span class="material-symbols-outlined text-[32px]">school</span>
                </div>
            @endif
            <div class="relative z-10">
                <h3 class="font-title-lg text-title-lg text-on-surface">{{ $student->name ?? 'Belum pilih siswa' }}</h3>
                <div class="flex flex-wrap gap-4 mt-1">
                    <span class="font-label-mono text-label-mono text-on-surface-variant flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">badge</span>
                        NIS: {{ $student->nis ?? '-' }}
                    </span>
                    <span class="font-label-mono text-label-mono text-on-surface-variant flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">class</span>
                        Kelas: {{ $student->classroom->name ?? '-' }}
                    </span>
                </div>
            </div>
            @if ($student)
                <div class="ml-auto relative z-10">
                    @if ($student->is_active)
                        <span class="px-3 py-1 rounded-full border border-tertiary-fixed-dim text-tertiary-container bg-tertiary-fixed/20 font-label-mono text-label-mono">Aktif</span>
                    @else
                        <span class="px-3 py-1 rounded-full border border-error/20 text-on-error-container bg-error-container font-label-mono text-label-mono">Nonaktif</span>
                    @endif
                </div>
            @endif
        </section>

        {{-- Outstanding Bill Card --}}
        <section class="bg-primary rounded-xl p-6 md:p-8 shadow-sm text-on-primary relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary-container rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/4"></div>
            <div class="relative z-10">
                <div class="flex flex-wrap gap-2 items-start justify-between mb-6">
                    <div class="min-w-0">
                        <p class="font-label-mono text-label-mono text-primary-fixed-dim uppercase tracking-wider mb-1">Tagihan Bulan Ini</p>
                        <h4 class="font-title-lg text-title-lg">SPP - {{ $months[now()->month] ?? now()->month }} {{ now()->year }}</h4>
                    </div>
                    @if ($currentFee && ! $currentFee->is_paid)
                        <span class="px-3 py-1 rounded-full bg-error-container text-on-error-container font-label-mono text-label-mono flex items-center gap-1 border border-error/20">
                            <span class="material-symbols-outlined text-[14px]">pending_actions</span> Belum Lunas
                        </span>
                    @else
                        <x-chip color="tertiary">Lunas</x-chip>
                    @endif
                </div>
                <div class="mb-8">
                    <p class="font-data-table text-data-table text-primary-fixed-dim mb-1">Total Tagihan</p>
                    <div class="flex items-baseline gap-2">
                        <span class="font-title-lg text-title-lg">Rp</span>
                        <span class="text-[40px] md:text-[48px] leading-tight md:leading-[56px] font-bold font-data-table tracking-tight break-all">
                            {{ number_format((float) ($currentFee->amount ?? 0), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-primary-fixed-variant/30 rounded-lg p-4 backdrop-blur-sm border border-primary-fixed/10">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary-fixed">calendar_today</span>
                        <div>
                            <p class="font-label-mono text-label-mono text-primary-fixed-dim">Jatuh Tempo</p>
                            <p class="font-body-md text-body-md font-semibold">
                                {{ $currentFee ? \Carbon\Carbon::create($currentFee->year, $currentFee->month, 10)->locale('id')->isoFormat('Do MMMM YYYY') : '—' }}
                            </p>
                        </div>
                    </div>
                    @if (auth()->user()->isBendahara())
                        @if ($currentFee && ! $currentFee->is_paid)
                            <form method="POST" action="{{ route('spp.mark-paid') }}" enctype="multipart/form-data" class="w-full sm:w-auto space-y-3 rounded-lg p-3 bg-primary-fixed/10 border border-primary-fixed/20">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <input type="hidden" name="month" value="{{ now()->month }}">
                                <input type="hidden" name="year" value="{{ now()->year }}">
                                <input type="hidden" name="amount" value="{{ $currentFee->amount ?? 100000 }}">
                                <div>
                                    <label class="font-label-mono text-label-mono text-primary-fixed-dim block mb-1">METODE</label>
                                    <select name="method" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-1.5 px-2 text-body-md w-full">
                                        <option value="cash">Tunai</option>
                                        <option value="transfer">Transfer Bank</option>
                                        <option value="virtual_account">Virtual Account</option>
                                        <option value="qris">QRIS / E-Wallet</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="font-label-mono text-label-mono text-primary-fixed-dim block mb-1">BUKTI (wajib)</label>
                                    <input type="file" name="proof" accept="image/jpeg,image/png" required
                                        class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-1.5 px-2 text-body-md w-full text-xs">
                                </div>
                                <x-button variant="secondary" type="submit" icon="arrow_forward" icon-position="right" class="rounded-full w-full">
                                    Bayar Sekarang
                                </x-button>
                            </form>
                        @endif
                    @else
                        @if ($currentFee && ! $currentFee->is_paid)
                            <div class="w-full sm:max-w-sm space-y-3 rounded-lg p-4 bg-on-primary-container/10 border border-primary-fixed/20">
                                <p class="font-label-mono text-label-mono text-primary-fixed flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px]">info</span>
                                    Cara Membayar SPP
                                </p>
                                <p class="font-body-md text-body-md text-primary-fixed-dim leading-relaxed">
                                    {{ config('school.payment_instructions') }}
                                </p>
                                <div class="space-y-2 pt-1">
                                    @foreach(config('school.payment_accounts', []) as $account)
                                        <div class="rounded-lg bg-primary-fixed/10 border border-primary-fixed/15 p-3">
                                            <p class="font-label-mono text-label-mono text-primary-fixed-dim uppercase tracking-wider flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[14px]">{{ $account['type'] === 'qris' ? 'qr_code_scanner' : 'account_balance' }}</span>
                                                {{ $account['bank'] }}
                                            </p>
                                            <p class="font-data-table text-data-table text-primary-fixed font-semibold mt-1 break-all">{{ $account['account_number'] }}</p>
                                            <p class="font-label-mono text-label-mono text-primary-fixed-dim text-xs mt-0.5">a.n. {{ $account['account_name'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="font-label-mono text-label-mono text-primary-fixed-dim text-xs flex items-start gap-1">
                                    <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                                    Status akan diperbarui bendahara setelah pembayaran dikonfirmasi.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </section>

        {{-- Payment Methods --}}
        <section class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/50">
            <h3 class="font-title-lg text-title-lg text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">account_balance_wallet</span> Metode Pembayaran
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="cursor-pointer">
                    <input checked class="peer sr-only" name="payment_method" type="radio">
                    <div class="p-4 rounded-lg border-2 border-outline-variant peer-checked:border-primary peer-checked:bg-primary-container/10 transition-all hover:bg-surface-variant/30 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-surface-variant flex items-center justify-center text-on-surface-variant shrink-0">
                            <span class="material-symbols-outlined">account_balance</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-body-md text-body-md font-semibold text-on-surface">Virtual Account</p>
                            <p class="font-label-mono text-label-mono text-on-surface-variant">BCA, Mandiri, BNI, BRI</p>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-outline-variant peer-checked:border-primary peer-checked:bg-primary flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                        </div>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input class="peer sr-only" name="payment_method" type="radio">
                    <div class="p-4 rounded-lg border-2 border-outline-variant peer-checked:border-primary peer-checked:bg-primary-container/10 transition-all hover:bg-surface-variant/30 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-surface-variant flex items-center justify-center text-on-surface-variant shrink-0">
                            <span class="material-symbols-outlined">qr_code_scanner</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-body-md text-body-md font-semibold text-on-surface">E-Wallet / QRIS</p>
                            <p class="font-label-mono text-label-mono text-on-surface-variant">Gopay, OVO, Dana</p>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-outline-variant flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full bg-white opacity-0 transition-opacity"></div>
                        </div>
                    </div>
                </label>
            </div>
        </section>
    </div>

    {{-- Right Column: History --}}
    <div class="lg:col-span-5 flex flex-col">
        <section class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant flex-1 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-surface-variant bg-surface-container/30">
                <div class="flex justify-between items-center">
                    <h3 class="font-title-lg text-title-lg text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">history</span> Riwayat Pembayaran
                    </h3>
                    <a class="text-primary hover:bg-primary-container/20 p-2 rounded-full transition-colors" href="{{ route('spp.index') }}" title="Filter Riwayat">
                        <span class="material-symbols-outlined">filter_list</span>
                    </a>
                </div>
                <p class="font-label-mono text-label-mono text-on-surface-variant mt-1">Tahun Ajaran {{ now()->year - 1 }}/{{ now()->year }}</p>
            </div>

            <div class="flex-1 overflow-y-auto p-2 max-h-[420px]">
                @forelse($paidFees as $fee)
                    <div class="flex items-center justify-between p-4 hover:bg-surface-variant/20 rounded-lg transition-colors border-b border-surface-variant last:border-0">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-tertiary-container/10 flex items-center justify-center text-tertiary-container shrink-0 mt-1">
                                <span class="material-symbols-outlined">check_circle</span>
                            </div>
                            <div>
                                <p class="font-body-md text-body-md font-semibold text-on-surface">SPP - {{ $months[$fee->month] ?? $fee->month }} {{ $fee->year }}</p>
                                <div class="flex flex-col gap-1 mt-1">
                                    <span class="font-data-table text-data-table text-on-surface-variant">Rp {{ number_format((float) $fee->amount, 0, ',', '.') }}</span>
                                    <span class="font-label-mono text-label-mono text-outline flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                                        {{ \Carbon\Carbon::parse($fee->paid_at)->locale('id')->isoFormat('Do MMMM YYYY, HH:mm') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded border border-tertiary-fixed-dim text-tertiary-container bg-tertiary-fixed/20 font-label-mono text-label-mono text-[10px] tracking-widest font-bold">LUNAS</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-on-surface-variant font-body-md">
                        Belum ada riwayat pembayaran.
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t border-surface-variant bg-surface-container-lowest text-center">
                <a class="text-primary font-title-lg text-title-lg hover:underline decoration-primary underline-offset-4" href="{{ route('spp.index') }}">Lihat Semua Riwayat</a>
            </div>
        </section>
    </div>
</div>
@endsection