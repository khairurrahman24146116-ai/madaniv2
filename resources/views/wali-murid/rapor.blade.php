@extends('layouts.app')

@section('content')
<div class="mb-lg">
    <a href="{{ route('wali-murid.dashboard') }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80 mb-md">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
    </a>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-md">
        <div>
            <h2 class="text-headline-lg-mobile md:text-headline-lg font-bold text-on-surface">Rapor - {{ $student->name }}</h2>
            <p class="text-body-md text-on-surface-variant mt-1">NIS: {{ $student->nis }} | {{ $student->classroom->name ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-sm">
            <select id="pdf-semester" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
                <option value="ganjil">Ganjil</option>
                <option value="genap">Genap</option>
            </select>
            <select id="pdf-tahun" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
                <option value="2025/2026">2025/2026</option>
                <option value="2026/2027">2026/2027</option>
            </select>
            <a href="#" id="btn-cetak-pdf" class="inline-flex items-center gap-2 px-md py-2 bg-primary text-on-primary rounded-lg text-label-md hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-[18px]">download</span> Cetak PDF
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('btn-cetak-pdf').addEventListener('click', function(e) {
        e.preventDefault();
        const semester = document.getElementById('pdf-semester').value;
        const tahun = document.getElementById('pdf-tahun').value;
        const url = "{{ route('rapor.pdf') }}?student_id={{ $student->id }}&semester=" + semester + "&academic_year=" + encodeURIComponent(tahun);
        window.open(url, '_blank');
    });
</script>
@endpush

@if($rapor->isEmpty())
<x-card variant="default" padding="lg" class="text-center">
    <span class="material-symbols-outlined text-[48px] text-on-surface-variant">assignment</span>
    <p class="text-body-md text-on-surface-variant mt-md">Belum ada data rapor untuk siswa ini.</p>
</x-card>
@else
<div class="grid grid-cols-1 gap-lg">
    @foreach($rapor as $item)
    <x-card variant="default" padding="lg">
        <div class="flex items-start justify-between mb-md">
            <div>
                <h3 class="text-title-lg text-on-surface font-bold">{{ $item['subject'] }}</h3>
                <p class="text-body-md text-on-surface-variant">Nilai Akhir: <strong class="text-primary">{{ number_format($item['final_grade'], 2) }}</strong></p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high border-b border-outline-variant">
                        <th class="px-md py-sm text-label-md text-on-surface-variant uppercase">Komponen</th>
                        <th class="px-md py-sm text-label-md text-on-surface-variant uppercase text-right">Nilai</th>
                        <th class="px-md py-sm text-label-md text-on-surface-variant uppercase text-right">Bobot</th>
                        <th class="px-md py-sm text-label-md text-on-surface-variant uppercase text-right">Nilai Terbobot</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach($item['components'] as $comp)
                    <tr>
                        <td class="px-md py-sm text-body-md text-on-surface">{{ $comp['name'] }}</td>
                        <td class="px-md py-sm text-body-md text-on-surface-variant text-right">{{ number_format($comp['value'], 2) }}</td>
                        <td class="px-md py-sm text-body-md text-on-surface-variant text-right">{{ $comp['weight'] }}%</td>
                        <td class="px-md py-sm text-body-md text-on-surface-variant text-right">{{ number_format($comp['weighted'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endforeach
</div>
@endif
@endsection
