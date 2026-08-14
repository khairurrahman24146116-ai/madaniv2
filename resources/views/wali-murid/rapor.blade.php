@extends('layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('wali-murid.dashboard') }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80 mb-4">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
    </a>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-headline-lg-mobile md:text-headline-lg font-bold text-on-surface">Rapor - {{ $student->name }}</h2>
            <p class="text-body-md text-on-surface-variant mt-1">NIS: {{ $student->nis }} | {{ $student->classroom->name ?? '-' }}</p>
        </div>
        <form method="GET" action="{{ route('wali-murid.rapor', $student->id) }}" class="flex items-center gap-2">
            <select name="semester" id="pdf-semester" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md" onchange="this.form.submit()">
                <option value="ganjil" @selected($semester === 'ganjil')>Ganjil</option>
                <option value="genap" @selected($semester === 'genap')>Genap</option>
            </select>
            <select name="academic_year" id="pdf-tahun" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md" onchange="this.form.submit()">
                <option value="2025/2026" @selected($academicYear === '2025/2026')>2025/2026</option>
                <option value="2026/2027" @selected($academicYear === '2026/2027')>2026/2027</option>
            </select>
            <a href="#" id="btn-cetak-pdf" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-lg text-label-md hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-[18px]">download</span> Cetak PDF
            </a>
        </form>
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

@if(empty($rapor['subjects']))
<x-card variant="default" padding="lg" class="text-center">
    <span class="material-symbols-outlined text-[48px] text-on-surface-variant">assignment</span>
    <p class="text-body-md text-on-surface-variant mt-4">Belum ada data rapor untuk {{ $student->name }} pada semester {{ ucfirst($semester) }} {{ $academicYear }}.</p>
    <p class="text-body-md text-on-surface-variant mt-2">Rapor akan otomatis tersedia setelah guru pengampu mengisi nilai pada menu <strong>Nilai</strong>.</p>
</x-card>
@else
<div class="grid grid-cols-1 gap-6">
    @foreach($rapor['subjects'] as $item)
    <x-card variant="default" padding="lg">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-title-lg text-on-surface font-bold">{{ $item['subject_name'] }}</h3>
                <p class="text-body-md text-on-surface-variant">Kode: {{ $item['subject_code'] }}</p>
            </div>
            @if($item['final_grade'] !== null)
                <x-badge variant="{{ $item['passed'] ? 'lulus' : 'tidak-lulus' }}">{{ $item['passed'] ? 'Tuntas' : 'Belum Tuntas' }}</x-badge>
            @else
                <x-badge variant="neutral">Nilai belum diisi guru pengampu</x-badge>
            @endif
        </div>

        @if($item['final_grade'] !== null)
        <p class="text-body-md text-on-surface-variant mb-4">Nilai Akhir: <strong class="text-primary">{{ number_format($item['final_grade'], 2) }}</strong></p>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high border-b border-outline-variant">
                        <th class="px-4 py-2 text-label-md text-on-surface-variant uppercase">Komponen</th>
                        <th class="px-4 py-2 text-label-md text-on-surface-variant uppercase text-right">Nilai</th>
                        <th class="px-4 py-2 text-label-md text-on-surface-variant uppercase text-right">Bobot</th>
                        <th class="px-4 py-2 text-label-md text-on-surface-variant uppercase text-right">Nilai Terbobot</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach($item['components'] as $code => $comp)
                    <tr>
                        <td class="px-4 py-2 text-body-md text-on-surface">{{ $comp['name'] }}</td>
                        <td class="px-4 py-2 text-body-md text-on-surface-variant text-right">{{ $comp['average_score'] !== null ? number_format($comp['average_score'], 2) : '-' }}</td>
                        <td class="px-4 py-2 text-body-md text-on-surface-variant text-right">{{ $comp['weight'] }}%</td>
                        <td class="px-4 py-2 text-body-md text-on-surface-variant text-right">{{ $comp['weighted_score'] !== null ? number_format($comp['weighted_score'], 2) : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-body-md text-on-surface-variant">
            Guru pengampu belum mengisi nilai untuk mata pelajaran ini. Rapor akan tampil otomatis setelah nilai diinput pada menu <strong>Nilai</strong>.
        </p>
        @endif
    </x-card>
    @endforeach
</div>

<div class="mt-6 flex items-center justify-between gap-4">
    <div>
        <p class="text-body-md text-on-surface-variant">Rata-rata Keseluruhan</p>
        <p class="text-headline-lg font-bold text-primary">{{ $rapor['overall_average'] !== null ? number_format($rapor['overall_average'], 2) : '-' }}</p>
    </div>
    <div class="text-right">
        <p class="text-body-md text-on-surface-variant">Status</p>
        <x-badge variant="{{ $rapor['passed_all'] === true ? 'lulus' : 'tidak-lulus' }}">
            {{ $rapor['passed_all'] === true ? 'Tuntas Semua' : ($rapor['passed_all'] === false ? 'Belum Tuntas' : 'Belum Tersedia') }}
        </x-badge>
    </div>
</div>
@endif
@endsection
