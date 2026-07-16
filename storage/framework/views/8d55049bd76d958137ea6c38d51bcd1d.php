<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Jadwal Pelajaran','subtitle' => 'Jadwal mingguan blok sore (14:00-16:00)','icon' => 'calendar_month','actions' => [
        ['type' => 'button', 'label' => 'Mobile View', 'icon' => 'phone_android', 'variant' => 'secondary', 'href' => route('schedules.mobile')],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Jadwal Pelajaran','subtitle' => 'Jadwal mingguan blok sore (14:00-16:00)','icon' => 'calendar_month','actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['type' => 'button', 'label' => 'Mobile View', 'icon' => 'phone_android', 'variant' => 'secondary', 'href' => route('schedules.mobile')],
    ])]); ?>
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

<div class="bg-surface-container-low rounded-xl p-lg border border-outline-variant overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
                <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Hari</th>
                <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Waktu</th>
                <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Mata Pelajaran</th>
                <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Guru</th>
                <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Ruang</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            <?php $__empty_1 = true; $__currentLoopData = $scheduleGrid ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="px-lg py-md text-body-md text-on-surface"><?php echo e(ucfirst($s['day'])); ?></td>
                <td class="px-lg py-md text-body-md text-on-surface"><?php echo e($s['start_time']); ?> - <?php echo e($s['end_time']); ?></td>
                <td class="px-lg py-md text-body-md text-on-surface font-medium"><?php echo e($s['subject']); ?></td>
                <td class="px-lg py-md text-body-md text-on-surface-variant"><?php echo e($s['teacher']); ?></td>
                <td class="px-lg py-md text-body-md text-on-surface-variant"><?php echo e($s['class'] ?? $s['teacher_short']); ?></td>
                <td class="px-lg py-md text-body-md text-on-surface-variant"><?php echo e($s['room']); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="text-center py-xl text-on-surface-variant">Belum ada jadwal.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/schedules/index.blade.php ENDPATH**/ ?>