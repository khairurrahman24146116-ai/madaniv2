@extends('layouts.app')

@section('content')
<x-page-header 
    title="Input Nilai" 
    subtitle="Input dan kelola nilai akademik harian siswa"
    icon="grade"
    :actions="[
        ['type' => 'button', 'label' => 'Import Excel', 'icon' => 'upload_file', 'variant' => 'outline', 'onclick' => 'openImportModal()'],
    ]"
/>

{{-- Selection --}}
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter mb-lg">
    <form method="GET" action="{{ route('scores.create') }}" class="md:col-span-8 x-card p-md">
        <label class="block text-label-md text-on-surface-variant mb-xs" for="teacher_subject_id">MATA PELAJARAN DAN KELAS</label>
        <select id="teacher_subject_id" name="teacher_subject_id" onchange="this.form.submit()" class="w-full rounded-lg border-outline-variant bg-surface-bright text-on-surface focus:ring-primary focus:border-primary py-2 px-3 text-body-md">
            @forelse($teacherSubjects as $mapping)
            <option value="{{ $mapping->id }}" @selected($selectedMapping?->id === $mapping->id)>
                {{ $mapping->subject->name }} - {{ $mapping->classroom->name }}
            </option>
            @empty
            <option>Tidak ada kelas yang dapat diinput</option>
            @endforelse
        </select>
    </form>
    <div class="md:col-span-4 bg-primary-container text-on-primary-container rounded-xl p-md flex flex-col justify-between">
        <div>
            <h3 class="text-label-md opacity-80 uppercase tracking-wider">Bobot Nilai Saat Ini</h3>
            <div class="mt-xs flex items-baseline gap-xs">
                <span class="text-[24px] font-bold">{{ count($students) }}</span>
                <span class="text-xs opacity-70">SISWA AKTIF</span>
            </div>
        </div>
    </div>
</div>

{{-- Segmented Control --}}
<div class="mb-lg flex flex-col sm:flex-row sm:items-center justify-between gap-md">
    <div class="inline-flex bg-surface-container-high p-1 rounded-xl shadow-inner border border-outline-variant">
        @foreach(['tugas' => 'Tugas', 'ph' => 'Harian', 'uts' => 'UTS', 'uas' => 'UAS'] as $code => $label)
        <button type="button" data-component="{{ $code }}" class="score-component px-6 py-2 rounded-lg text-label-md @if($loop->first) bg-primary text-on-primary shadow-sm @else text-on-surface-variant hover:bg-surface-container @endif transition-all">{{ $label }}</button>
        @endforeach
    </div>
</div>

{{-- Score Table --}}
<x-card variant="default" class="overflow-x-auto">
    <table class="w-full min-w-[720px] text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
                <th class="px-md py-4 text-label-md text-on-surface-variant">SISWA</th>
                <th class="px-md py-4 text-label-md text-on-surface-variant text-center w-24">NIS</th>
                <th class="px-md py-4 text-label-md text-on-surface-variant text-center w-32">NILAI (0-100)</th>
                <th class="px-md py-4 text-label-md text-on-surface-variant text-center w-32">STATUS</th>
                <th class="px-md py-4 text-label-md text-on-surface-variant text-center w-20">NA*</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            @foreach($students ?? [] as $student)
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="px-md py-4">
                    <div class="flex items-center gap-md">
                        <div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                        <div>
                            <p class="text-body-lg text-on-surface font-semibold">{{ $student->name }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-md py-4 text-center text-body-md text-on-surface-variant">{{ $student->nis }}</td>
                <td class="px-md py-4">
                    <input class="w-full text-center rounded-lg border-outline-variant bg-surface-bright focus:ring-primary focus:border-primary py-2 text-body-md score-input" data-student-id="{{ $student->id }}" max="100" min="0" placeholder="Nilai" type="number">
                </td>
                <td class="px-md py-4 text-center">
                    <span class="px-3 py-1 bg-surface-container text-on-surface-variant rounded-full text-xs font-semibold">BELUM</span>
                </td>
                <td class="px-md py-4 text-center font-bold text-on-surface-variant">-</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-card>

{{-- Action Bar --}}
<div class="mt-lg grid grid-cols-1 md:grid-cols-12 gap-gutter items-end">
    <div class="md:col-span-8 bg-surface-container-low rounded-lg p-md border border-outline-variant flex flex-wrap gap-xl">
        <div>
            <p class="text-caption text-on-surface-variant">RATA-RATA KELAS</p>
            <p class="text-headline-md text-primary" id="avg-score">-</p>
        </div>
        <div class="w-px h-10 bg-outline-variant self-center"></div>
        <div>
            <p class="text-caption text-on-surface-variant">TERTINGGI / TERENDAH</p>
            <p class="text-headline-md text-on-surface" id="range-score">-</p>
        </div>
        <div class="w-px h-10 bg-outline-variant self-center"></div>
        <div>
            <p class="text-caption text-on-surface-variant">KETUNTASAN</p>
            <p class="text-headline-md text-green-700" id="pass-rate">-</p>
        </div>
    </div>
    <div class="md:col-span-4">
        <x-button id="save-scores" variant="primary" size="xl" type="button" icon="save" icon-position="left" class="w-full" :disabled="!$selectedMapping || count($students) === 0">
            Simpan Nilai
        </x-button>
        <p id="score-feedback" class="text-center text-xs text-on-surface-variant mt-sm" role="status">Masukkan nilai, lalu simpan komponen yang dipilih.</p>
    </div>
</div>

{{-- Import Modal --}}
<x-modal id="import-modal" title="Import Nilai dari Excel" size="md">
    <form id="import-form" action="{{ route('scores.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <x-form-input type="file" name="excel_file" label="File Excel (.xlsx)" required hint="Format: NIS, Nilai (header: nis,value)" accept=".xlsx,.xls,.csv" />
        <input type="hidden" name="component_code" id="import-component" value="tugas">
        <input type="hidden" name="teacher_subject_id" id="import-ts" value="{{ $selectedMapping?->id }}">
        <input type="hidden" name="semester" value="ganjil">
        <input type="hidden" name="academic_year" value="2025/2026">
        <p id="import-feedback" class="text-xs text-on-surface-variant"></p>
        <div class="flex justify-end gap-sm pt-4">
            <x-button variant="ghost" type="button" onclick="closeModal('import-modal')">Batal</x-button>
            <x-button variant="primary" type="submit" icon="upload_file" id="import-submit">Import</x-button>
        </div>
    </form>
</x-modal>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.score-input').forEach(input => {
        input.addEventListener('input', function() {
            let val = parseInt(this.value);
            if (val > 100) this.value = 100;
            if (val < 0 || isNaN(val)) this.value = 0;
            updateStats();
        });
    });

    function updateStats() {
        const inputs = document.querySelectorAll('.score-input');
        let values = [];
        inputs.forEach(inp => {
            let v = parseInt(inp.value);
            if (!isNaN(v) && v > 0) values.push(v);
        });
        if (values.length === 0) {
            document.getElementById('avg-score').textContent = '-';
            document.getElementById('range-score').textContent = '-';
            document.getElementById('pass-rate').textContent = '-';
            return;
        }
        const sum = values.reduce((a, b) => a + b, 0);
        const avg = (sum / values.length).toFixed(1);
        const max = Math.max(...values);
        const min = Math.min(...values);
        const pass = values.filter(v => v >= 75).length;
        const rate = ((pass / values.length) * 100).toFixed(0);
        document.getElementById('avg-score').textContent = avg;
        document.getElementById('range-score').textContent = max + ' / ' + min;
        document.getElementById('pass-rate').textContent = rate + '%';
    }

    let component = 'tugas';
    const subjectId = {{ $selectedMapping?->subject_id ?? 'null' }};
    const saveButton = document.getElementById('save-scores');
    const feedback = document.getElementById('score-feedback');

    document.querySelectorAll('.score-component').forEach(button => {
        button.addEventListener('click', () => {
            component = button.dataset.component;
            document.querySelectorAll('.score-component').forEach(item => {
                item.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
                item.classList.add('text-on-surface-variant');
            });
            button.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
            button.classList.remove('text-on-surface-variant');
        });
    });

    saveButton?.addEventListener('click', async () => {
        const scores = [...document.querySelectorAll('.score-input')]
            .filter(input => input.value !== '')
            .map(input => ({ student_id: Number(input.dataset.studentId), value: Number(input.value) }));

        if (!scores.length) {
            feedback.textContent = 'Masukkan setidaknya satu nilai sebelum menyimpan.';
            feedback.className = 'text-center text-xs text-error mt-sm';
            return;
        }

        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Menyimpan...';
        try {
            const response = await fetch('{{ url('/scores/batch') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    subject_id: subjectId,
                    component_code: component,
                    semester: 'ganjil',
                    academic_year: '2025/2026',
                    scores,
                }),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Nilai gagal disimpan.');

            feedback.textContent = result.message;
            feedback.className = 'text-center text-xs text-green-700 mt-sm';
            saveButton.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Nilai Tersimpan';
        } catch (error) {
            feedback.textContent = error.message;
            feedback.className = 'text-center text-xs text-error mt-sm';
            saveButton.innerHTML = '<span class="material-symbols-outlined">save</span> Simpan Nilai';
        } finally {
            saveButton.disabled = false;
        }
    });

    const importForm = document.getElementById('import-form');
    const importFeedback = document.getElementById('import-feedback');
    const importSubmit = document.getElementById('import-submit');

    importForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(importForm);
        formData.set('component_code', component);
        formData.set('teacher_subject_id', document.getElementById('teacher_subject_id')?.value || '');

        importSubmit.disabled = true;
        importSubmit.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Mengimport...';
        importFeedback.textContent = '';
        importFeedback.className = 'text-xs text-on-surface-variant';

        try {
            const response = await fetch(importForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Import gagal.');

            importFeedback.textContent = result.message;
            importFeedback.className = 'text-xs text-green-700 mt-sm';
            importSubmit.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Selesai';
            setTimeout(() => location.reload(), 1500);
        } catch (error) {
            importFeedback.textContent = error.message;
            importFeedback.className = 'text-xs text-error mt-sm';
            importSubmit.innerHTML = '<span class="material-symbols-outlined">upload_file</span> Import';
        } finally {
            importSubmit.disabled = false;
        }
    });

    function openImportModal() {
        document.getElementById('import-modal').classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endpush