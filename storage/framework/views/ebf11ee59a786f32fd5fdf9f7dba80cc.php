<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Data Kelas','subtitle' => 'Kelola kelas X, XI, XII','icon' => 'meeting_room','actions' => [['label' => 'Tambah Kelas', 'icon' => 'add', 'href' => route('admin.classrooms.create')]]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Data Kelas','subtitle' => 'Kelola kelas X, XI, XII','icon' => 'meeting_room','actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['label' => 'Tambah Kelas', 'icon' => 'add', 'href' => route('admin.classrooms.create')]])]); ?>
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

<?php if(session('success')): ?>
<div class="mb-lg p-md bg-green-50 text-green-800 rounded-xl text-[14px] flex items-start gap-3 border border-green-200">
    <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">check_circle</span>
    <div><?php echo e(session('success')); ?></div>
</div>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['variant' => 'default']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'default']); ?>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Tingkat</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Tahun Ajaran</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Siswa</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                <?php $__empty_1 = true; $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold"><?php echo e($c->name); ?></td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant"><?php echo e($c->grade); ?></td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant"><?php echo e($c->academic_year); ?></td>
                    <td class="px-lg py-md text-center text-body-md text-on-surface-variant"><?php echo e($c->students_count); ?></td>
                    <td class="px-lg py-md text-right">
                        <a href="<?php echo e(route('admin.classrooms.edit', $c)); ?>" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <form action="<?php echo e(route('admin.classrooms.destroy', $c)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus kelas <?php echo e($c->name); ?>?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80 ml-md">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center py-xl text-on-surface-variant">Belum ada kelas.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/admin/classrooms/index.blade.php ENDPATH**/ ?>