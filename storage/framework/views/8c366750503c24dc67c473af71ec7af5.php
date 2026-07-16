<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Riwayat Absensi','subtitle' => 'Log kehadiran harian siswa','icon' => 'history']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Riwayat Absensi','subtitle' => 'Log kehadiran harian siswa','icon' => 'history']); ?>
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


<form action="<?php echo e(route('attendances.index')); ?>" method="GET" class="flex flex-wrap gap-md mb-lg p-md bg-surface-container-low rounded-xl border border-outline-variant">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">TANGGAL</label>
        <input type="date" name="date" value="<?php echo e(request('date')); ?>" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">STATUS</label>
        <select name="status" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md">
            <option value="">Semua</option>
            <option value="H" <?php if(request('status') === 'H'): echo 'selected'; endif; ?>>Hadir</option>
            <option value="S" <?php if(request('status') === 'S'): echo 'selected'; endif; ?>>Sakit</option>
            <option value="I" <?php if(request('status') === 'I'): echo 'selected'; endif; ?>>Izin</option>
            <option value="A" <?php if(request('status') === 'A'): echo 'selected'; endif; ?>>Alpa</option>
        </select>
    </div>
    <div class="self-end flex gap-sm">
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'primary','icon' => 'filter_list']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','icon' => 'filter_list']); ?>Filter <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
        <a href="<?php echo e(route('attendances.index')); ?>" class="px-lg py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors inline-flex items-center">
            Reset
        </a>
    </div>
</form>


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
    <div class="p-lg border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
        <h3 class="text-headline-md text-on-surface">Data Absensi</h3>
        <a href="<?php echo e(route('attendances.export-csv')); ?>?<?php echo e(http_build_query(request()->query())); ?>" class="flex items-center gap-sm px-md py-2 border border-outline-variant rounded-lg text-label-md text-on-surface-variant hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-[18px]">download</span> Export CSV
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high border-b border-outline-variant">
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Tanggal</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">NIS</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Kelas</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase">Mapel</th>
                    <th class="px-lg py-md text-label-md text-on-surface-variant uppercase text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                <?php $__empty_1 = true; $__currentLoopData = $attendances ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-lg py-md text-body-md text-on-surface"><?php echo e($a->date); ?></td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant"><?php echo e($a->student->nis); ?></td>
                    <td class="px-lg py-md text-body-md text-on-surface font-semibold"><?php echo e($a->student->name); ?></td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant"><?php echo e($a->schedule->teacherSubject->classroom->name ?? '-'); ?></td>
                    <td class="px-lg py-md text-body-md text-on-surface-variant"><?php echo e($a->schedule->teacherSubject->subject->name ?? '-'); ?></td>
                    <td class="px-lg py-md text-center">
                        <?php
                        $badge = ['H' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300', 'S' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300', 'I' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300', 'A' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300'];
                        ?>
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo e($badge[$a->status] ?? 'bg-surface-container text-on-surface-variant'); ?>"><?php echo e($a->status); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center py-xl text-on-surface-variant">Belum ada data absensi.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(method_exists($attendances ?? [], 'links')): ?>
    <div class="p-lg border-t border-outline-variant">
        <?php echo e($attendances->links()); ?>

    </div>
    <?php endif; ?>
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/attendances/index.blade.php ENDPATH**/ ?>