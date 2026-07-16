<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Panel Admin','subtitle' => 'Manajemen data master sistem','icon' => 'admin_panel_settings']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Panel Admin','subtitle' => 'Manajemen data master sistem','icon' => 'admin_panel_settings']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-md">
    <a href="<?php echo e(route('admin.classrooms.index')); ?>" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">meeting_room</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Kelas</p>
                <p class="text-caption text-on-surface-variant"><?php echo e(\App\Models\Classroom::count()); ?> terdaftar</p>
            </div>
        </div>
    </a>
    <a href="<?php echo e(route('admin.subjects.index')); ?>" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-tertiary-container text-on-tertiary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">book</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Mapel</p>
                <p class="text-caption text-on-surface-variant"><?php echo e(\App\Models\Subject::count()); ?> terdaftar</p>
            </div>
        </div>
    </a>
    <a href="<?php echo e(route('admin.students.index')); ?>" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-secondary-container text-on-secondary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">people</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Siswa</p>
                <p class="text-caption text-on-surface-variant"><?php echo e(\App\Models\Student::count()); ?> terdaftar</p>
            </div>
        </div>
    </a>
    <a href="<?php echo e(route('admin.teacher-subjects.index')); ?>" class="block rounded-lg bg-surface-container-lowest border border-outline-variant p-lg hover:bg-surface-container-low transition-colors">
        <div class="flex items-center gap-md mb-sm">
            <div class="w-12 h-12 bg-error-container text-on-error-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">assignment_ind</span>
            </div>
            <div>
                <p class="text-headline-md font-semibold text-on-surface">Mapping</p>
                <p class="text-caption text-on-surface-variant"><?php echo e(\App\Models\TeacherSubject::count()); ?> pengajaran</p>
            </div>
        </div>
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>