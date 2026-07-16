@extends('layouts.app')

@section('content')
<div class="mb-lg">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-md">
        <div>
            <span class="text-label-md text-primary uppercase tracking-widest mb-xs">LAPORAN RESMI</span>
            <h2 class="text-headline-xl mt-1">E-Rapor Preview</h2>
            <p class="text-on-surface-variant text-body-md mt-2">Laporan Capaian Akhir Semester</p>
        </div>
    </div>
</div>

{{-- Student Profile & Attendance Recap --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-lg mb-xl">
    {{-- Student Card --}}
    <x-card variant="elevated" padding="xl" class="md:col-span-2 flex flex-col md:flex-row gap-xl items-start relative overflow-hidden">
        <div class="w-32 h-32 rounded-lg bg-surface-container overflow-hidden border-2 border-primary/10 shrink-0 flex items-center justify-center text-4xl font-bold text-primary">
            {{ strtoupper(substr($student->name ?? 'A', 0, 2)) }}
        </div>
        <div class="flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-xl gap-x-xl">
                <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1">Nama Siswa</p>
                    <p class="text-headline-md text-on-surface">{{ $student->name ?? 'Ahmad Zulfikar' }}</p>
                </div>
                <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1">NIS</p>
                    <p class="text-headline-md text-on-surface">{{ $student->nis ?? '00452910384' }}</p>
                </div>
                <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1">Kelas</p>
                    <p class="text-headline-md text-on-surface">{{ $student->classroom->name ?? 'XII - IPA 1' }}</p>
                </div>
                <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1">Semester</p>
                    <p class="text-headline-md text-on-surface">{{ $semester ?? 'Ganjil' }} {{ $academicYear ?? '2025/2026' }}</p>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Attendance Summary --}}
    <x-card variant="elevated" padding="lg">
        <h3 class="text-headline-md mb-lg border-b border-outline-variant pb-sm">Rekap Absensi</h3>
        <div class="space-y-4">
            @php
            $attStats = $attendanceStats ?? ['H' => 124, 'S' => 2, 'I' => 1, 'A' => 0];
            $colors = ['H' => ['bg-green-50', 'text-green-800', 'bg-green-100', 'text-green-700', '#065F46'],
                        'S' => ['bg-amber-50', 'text-amber-800', 'bg-amber-100', 'text-amber-700', '#92400E'],
                        'I' => ['bg-blue-50', 'text-blue-800', 'bg-blue-100', 'text-blue-700', '#1E40AF'],
                        'A' => ['bg-red-50', 'text-red-800', 'bg-red-100', 'text-red-700', '#991B1B']];
            @endphp
            @foreach(['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'] as $key => $label)
            <div class="flex items-center justify-between p-3 rounded-lg {{ $colors[$key][0] }}">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full {{ $colors[$key][2] }} {{ $colors[$key][3] }} flex items-center justify-center font-bold text-xs">{{ $key }}</span>
                    <span class="text-body-md font-semibold">{{ $label }}</span>
                </div>
                <span class="text-headline-md" style="color: {{ $colors[$key][4] }}">{{ $attStats[$key] }}</span>
            </div>
            @endforeach
        </div>
    </x-card>
</div>

{{-- Grade Table --}}
<x-card variant="elevated" padding="0" class="mb-xl">
    <div class="p-xl bg-surface-container-low flex justify-between items-center border-b border-outline-variant">
        <h3 class="text-headline-md">Capaian Akademik</h3>
        <span class="text-label-md bg-primary-container text-on-primary-container px-3 py-1 rounded-full">GPA: {{ number_format($gpa ?? 3.82, 2) }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="text-label-md text-on-surface-variant uppercase px-xl py-4">Mata Pelajaran</th>
                    <th class="text-label-md text-on-surface-variant uppercase px-xl py-4 text-center">KKM</th>
                    <th class="text-label-md text-on-surface-variant uppercase px-xl py-4 text-center">Nilai</th>
                    <th class="text-label-md text-on-surface-variant uppercase px-xl py-4 text-center">Grade</th>
                </tr>
            </thead>
            <tbody class="text-body-md">
                @forelse($grades ?? [] as $grade)
                <tr class="hover:bg-surface-container-low transition-colors border-b border-outline-variant">
                    <td class="px-xl py-4 font-semibold text-primary">{{ $grade['subject'] }}</td>
                    <td class="px-xl py-4 text-center">{{ $grade['kkm'] ?? 70 }}</td>
                    <td class="px-xl py-4 text-center font-bold">{{ $grade['score'] }}</td>
                    <td class="px-xl py-4 text-center">
                        @php
                        $s = $grade['score'] ?? 0;
                        $g = $s >= 90 ? 'A' : ($s >= 80 ? 'B' : ($s >= 70 ? 'C' : ($s >= 60 ? 'D' : 'E')));
                        $gc = ['A' => 'bg-green-100 text-green-800', 'B' => 'bg-blue-100 text-blue-800', 'C' => 'bg-amber-100 text-amber-800', 'D' => 'bg-orange-100 text-orange-800', 'E' => 'bg-red-100 text-red-800'];
                        @endphp
                        <span class="px-3 py-1 rounded font-bold {{ $gc[$g] }}">{{ $g }}</span>
                    </td>
                </tr>
                @empty
                @foreach(['Bahasa Arab', 'Fisika', 'Biologi', 'Matematika Wajib', 'Bahasa Inggris', 'PAI', 'Kimia'] as $i => $s)
                <tr class="hover:bg-surface-container-low transition-colors border-b border-outline-variant">
                    <td class="px-xl py-4 font-semibold text-primary">{{ $s }}</td>
                    <td class="px-xl py-4 text-center">75</td>
                    <td class="px-xl py-4 text-center font-bold">{{ [92, 88, 84, 95, 89, 91, 78][$i] }}</td>
                    <td class="px-xl py-4 text-center">
                        @php $g = ['A','A','B','A','A','A','C'][$i]; @endphp
                        <span class="px-3 py-1 rounded font-bold {{ ['A' => 'bg-green-100 text-green-800', 'B' => 'bg-blue-100 text-blue-800', 'C' => 'bg-amber-100 text-amber-800'][$g] }}">{{ $g }}</span>
                    </td>
                </tr>
                @endforeach
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- Export --}}
<div class="flex flex-col md:flex-row items-center justify-between bg-primary p-xl rounded-xl text-white gap-lg shadow-lg shadow-primary/20">
    <div class="text-center md:text-left">
        <h4 class="text-headline-lg">Finalisasi E-Rapor</h4>
        <p class="text-on-primary-container/80 mt-1">Generate dokumen PDF resmi yang ditandatangani digital.</p>
    </div>
    <a href="{{ url('scores/rapor-pdf') }}?student_id={{ request('student_id') }}&semester={{ request('semester', 'ganjil') }}&academic_year={{ request('academic_year', '2025/2026') }}" class="bg-surface-container-lowest text-primary px-8 py-4 rounded-xl font-bold flex items-center gap-3 hover:scale-105 active:scale-95 transition-all shadow-md">
        <span class="material-symbols-outlined">picture_as_pdf</span>
        Ekspor PDF Resmi
    </a>
</div>

<div class="mt-xl mb-20 text-center">
    <p class="text-on-surface-variant text-caption italic">
        Laporan digenerate pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} pukul {{ now()->format('H:i') }} WIB.<br>
        Terautentikasi secara digital oleh Madani-SMS.
    </p>
</div>
@endsection