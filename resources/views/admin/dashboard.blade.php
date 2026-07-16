@extends('layouts.app')

@section('content')
<x-page-header title="Panel Admin" subtitle="Manajemen data master sistem" icon="admin_panel_settings" />

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-md">
    <a href="{{ route('admin.classrooms.index') }}" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">meeting_room</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Kelas</p>
                <p class="text-caption text-on-surface-variant">{{ \App\Models\Classroom::count() }} terdaftar</p>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.subjects.index') }}" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-tertiary-container text-on-tertiary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">book</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Mapel</p>
                <p class="text-caption text-on-surface-variant">{{ \App\Models\Subject::count() }} terdaftar</p>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.students.index') }}" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-secondary-container text-on-secondary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">people</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Siswa</p>
                <p class="text-caption text-on-surface-variant">{{ \App\Models\Student::count() }} terdaftar</p>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.teacher-subjects.index') }}" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-error-container text-on-error-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">assignment_ind</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Mapping</p>
                <p class="text-caption text-on-surface-variant">{{ \App\Models\TeacherSubject::count() }} pengajaran</p>
            </div>
        </div>
    </a>
</div>
@endsection
