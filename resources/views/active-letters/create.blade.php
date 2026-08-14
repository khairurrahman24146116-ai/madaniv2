@extends('layouts.app')

@section('content')
<x-page-header 
    title="Ajukan Surat Aktif" 
    subtitle="Buat pengajuan surat keterangan aktif untuk siswa"
    icon="add"
    :actions="[
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('active-letters.index')],
    ]"
/>

<form action="{{ route('active-letters.store') }}" method="POST" class="max-w-3xl space-y-4">
    @csrf
    <x-card variant="default" padding="lg">
        <div class="space-y-4">
            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">SISWA</label>
                <select name="student_id" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full">
                    <option value="">Pilih Siswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                            {{ $student->name }} ({{ $student->nis }}) - {{ $student->classroom?->name ?? 'Tanpa Kelas' }}
                        </option>
                    @endforeach
                </select>
                @error('student_id') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-label-md text-on-surface-variant block mb-1">KEPERLUAN SURAT</label>
                <textarea name="purpose" rows="4" required class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md w-full" placeholder="Contoh: Keperluan pembuatan rekening bank, pendaftaran lomba, dll.">{{ old('purpose') }}</textarea>
                @error('purpose') <p class="text-error text-label-md mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </x-card>
    <div class="flex gap-2">
        <x-button variant="primary" type="submit" icon="send">Ajukan Surat</x-button>
        <a href="{{ route('active-letters.index') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center">Batal</a>
    </div>
</form>
@endsection
