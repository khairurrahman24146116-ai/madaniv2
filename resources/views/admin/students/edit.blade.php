@extends('layouts.app')

@section('content')
<x-page-header title="Edit Siswa" subtitle="{{ $student->name }}" icon="people"
    :actions="[['label' => 'Kembali', 'icon' => 'arrow_back', 'variant' => 'outline', 'href' => route('admin.students.index')]]" />

<x-card variant="default" padding="lg" class="max-w-2xl">
    <form method="POST" action="{{ route('admin.students.update', $student) }}">
        @csrf @method('PUT')
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-xs">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('nis')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-xs">JENIS KELAMIN</label>
                    <select name="gender" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="L" @selected(old('gender', $student->gender)=='L')>Laki-laki</option>
                        <option value="P" @selected(old('gender', $student->gender)=='P')>Perempuan</option>
                    </select>
                    @error('gender')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">NAMA LENGKAP</label>
                <input type="text" name="name" value="{{ old('name', $student->name) }}" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('name')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">KELAS</label>
                <select name="classroom_id" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" @selected(old('classroom_id', $student->classroom_id)==$c->id)>{{ $c->grade }} - {{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="text-error text-caption mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">TANGGAL LAHIR</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}"
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">ALAMAT</label>
                <textarea name="address" rows="2"
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('address', $student->address) }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-xs">NO. TELP</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                </div>
                <div>
                    <label class="block text-label-md text-on-surface-variant mb-xs">NO. TELP ORANG TUA</label>
                    <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}"
                        class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-label-md text-on-surface-variant mb-xs">NAMA ORANG TUA/WALI</label>
                <input type="text" name="parent_name" value="{{ old('parent_name', $student->parent_name) }}"
                    class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface px-4 py-3 text-body-md outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
            </div>
            <div class="flex gap-md pt-md border-t border-outline-variant">
                <x-button variant="primary" type="submit" icon="save">Simpan</x-button>
                <x-button variant="outline" href="{{ route('admin.students.index') }}">Batal</x-button>
            </div>
        </div>
    </form>
</x-card>
@endsection
