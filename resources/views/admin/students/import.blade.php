@extends('layouts.app')

@section('content')
<x-page-header 
    title="Import Siswa dari Excel" 
    subtitle="Tambah banyak siswa sekaligus"
    icon="upload_file"
    :actions="[
        ['type' => 'button', 'label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.students.index')],
    ]"
/>

<x-card variant="default" padding="lg" class="max-w-2xl mb-6">
    <h3 class="text-headline-md text-on-surface mb-4">Panduan Format File</h3>
    <p class="text-body-md text-on-surface-variant mb-2">File Excel/CSV harus memiliki kolom berikut:</p>
    <div class="bg-surface-container-low p-4 rounded-lg text-body-md text-on-surface font-mono">
        <div class="grid grid-cols-3 gap-2 text-label-md text-on-surface-variant mb-1">
            <span class="min-w-0">A</span><span class="min-w-0">B</span><span class="min-w-0 break-words">C</span>
        </div>
        <div class="grid grid-cols-3 gap-2 border-t border-outline-variant py-1 items-start">
            <span class="min-w-0">NIS</span><span class="min-w-0">Nama</span><span class="min-w-0 break-words">Jenis Kelamin (L/P)</span>
        </div>
        <div class="grid grid-cols-3 gap-2 border-t border-outline-variant py-1">
            <span>1001</span><span>Ahmad Fauzi</span><span>L</span>
        </div>
        <div class="grid grid-cols-3 gap-2 border-t border-outline-variant py-1">
            <span>1002</span><span>Siti Aminah</span><span>P</span>
        </div>
    </div>
    <a href="{{ route('admin.students.export', ['classroom_id' => request('classroom_id')]) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:underline mt-4">
        <span class="material-symbols-outlined text-[18px]">download</span> Download contoh format (Excel)
    </a>
</x-card>

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">KELAS TUJUAN</label>
                <select name="classroom_id" required class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih kelas</option>
                    @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" @selected(request('classroom_id')==$c->id)>{{ $c->grade }} - {{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-1">FILE EXCEL / CSV</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('file')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-4 pt-4 border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="upload">Import Siswa</x-button>
                <x-button variant="outline" href="{{ route('admin.students.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
