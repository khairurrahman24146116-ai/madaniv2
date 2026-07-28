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

<x-card variant="default" padding="lg" class="max-w-2xl mb-lg">
    <h3 class="text-headline-md text-on-surface mb-md">Panduan Format File</h3>
    <p class="text-body-md text-on-surface-variant mb-sm">File Excel/CSV harus memiliki kolom berikut:</p>
    <div class="bg-surface-container-low p-md rounded-lg text-body-md text-on-surface font-mono">
        <div class="grid grid-cols-3 gap-sm text-label-md text-on-surface-variant mb-xs">
            <span>A</span><span>B</span><span>C</span>
        </div>
        <div class="grid grid-cols-3 gap-sm border-t border-outline-variant py-xs">
            <span>NIS</span><span>Nama</span><span>Jenis Kelamin (L/P)</span>
        </div>
        <div class="grid grid-cols-3 gap-sm border-t border-outline-variant py-xs">
            <span>1001</span><span>Ahmad Fauzi</span><span>L</span>
        </div>
        <div class="grid grid-cols-3 gap-sm border-t border-outline-variant py-xs">
            <span>1002</span><span>Siti Aminah</span><span>P</span>
        </div>
    </div>
    <a href="{{ route('admin.students.export', ['classroom_id' => request('classroom_id')]) }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:underline mt-md">
        <span class="material-symbols-outlined text-[18px]">download</span> Download contoh format (Excel)
    </a>
</x-card>

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-md">
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">KELAS TUJUAN</label>
                <select name="classroom_id" required class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih kelas</option>
                    @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" @selected(request('classroom_id')==$c->id)>{{ $c->grade }} - {{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">FILE EXCEL / CSV</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('file')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-md pt-md border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="upload">Import Siswa</x-button>
                <x-button variant="outline" href="{{ route('admin.students.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
