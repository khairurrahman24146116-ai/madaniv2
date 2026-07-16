<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Data Mata Pelajaran','subtitle' => 'Kelola mata pelajaran','icon' => 'book','actions' => [['label' => 'Tambah Mapel', 'icon' => 'add', 'href' => route('admin.subjects.create')]]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Data Mata Pelajaran','subtitle' => 'Kelola mata pelajaran','icon' => 'book','actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['label' => 'Tambah Mapel', 'icon' => 'add', 'href' => route('admin.subjects.create')]])]); ?>
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
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kode</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Pengajar</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                <?php $__empty_1 = true; $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md"><span class="font-mono text-label-md bg-surface-container px-2 py-0.5 rounded border border-outline-variant"><?php echo e($s->code); ?></span></td>
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold"><?php echo e($s->name); ?></td>
                    <td class="px-lg py-md text-center text-body-md text-on-surface-variant"><?php echo e($s->teacher_subjects_count); ?></td>
                    <td class="px-lg py-md text-right">
                        <a href="<?php echo e(route('admin.subjects.edit', $s)); ?>" class="inline-flex items-center gap-1 text-label-md text-primary hover:text-primary/80">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                        </a>
                        <form action="<?php echo e(route('admin.subjects.destroy', $s)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus mapel <?php echo e($s->name); ?>?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="inline-flex items-center gap-1 text-label-md text-error hover:text-error/80 ml-md">
                                <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-center py-xl text-on-surface-variant">Belum ada mata pelajaran.</td></tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/admin/subjects/index.blade.php ENDPATH**/ ?>