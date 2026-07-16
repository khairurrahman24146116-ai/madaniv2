<?php $__env->startSection('content'); ?>
<?php
$schedule = $schedule ?? null;
$students = $students ?? [];
$date = $date ?? now()->format('Y-m-d');
$canEdit = $canEdit ?? true;
?>

<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Form Absensi','subtitle' => 'Input kehadiran siswa per jam pelajaran','icon' => 'how_to_reg','actions' => [
        ['type' => 'button', 'label' => 'Realtime', 'icon' => 'speed', 'variant' => 'secondary', 'href' => route('attendances.realtime')],
        ['type' => 'button', 'label' => 'Riwayat', 'icon' => 'history', 'variant' => 'outline', 'href' => route('attendances.index')],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Form Absensi','subtitle' => 'Input kehadiran siswa per jam pelajaran','icon' => 'how_to_reg','actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['type' => 'button', 'label' => 'Realtime', 'icon' => 'speed', 'variant' => 'secondary', 'href' => route('attendances.realtime')],
        ['type' => 'button', 'label' => 'Riwayat', 'icon' => 'history', 'variant' => 'outline', 'href' => route('attendances.index')],
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


<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['variant' => 'default','padding' => 'md','class' => 'mb-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'default','padding' => 'md','class' => 'mb-lg']); ?>
    <div class="flex justify-between items-start mb-sm">
        <div>
            <h2 class="text-headline-md text-on-surface"><?php echo e($schedule->teacherSubject->subject->name ?? 'Pilih Jadwal'); ?> - <?php echo e($schedule->teacherSubject->classroom->name ?? ''); ?></h2>
            <p class="text-body-md text-on-surface-variant"><?php echo e(\Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
        </div>
        <div class="bg-secondary-container text-on-secondary-container px-sm py-xs rounded-lg text-label-md">
            <?php echo e($schedule->start_time ?? '14:00'); ?> - <?php echo e($schedule->end_time ?? '14:50'); ?>

        </div>
    </div>
    <div class="flex flex-wrap gap-sm mt-md" id="summary-chips">
        <div class="flex items-center gap-xs px-md py-xs bg-surface-container rounded-full border border-outline-variant">
            <span class="text-label-md text-on-surface-variant uppercase">Total:</span>
            <span class="text-headline-md text-on-surface" id="count-total"><?php echo e(count($students)); ?></span>
        </div>
        <div class="flex items-center gap-xs px-md py-xs bg-green-50 text-green-700 rounded-full border border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800">
            <span class="text-label-md uppercase">Hadir:</span>
            <span class="text-headline-md" id="count-present">0</span>
        </div>
        <div class="flex items-center gap-xs px-md py-xs bg-amber-50 text-amber-700 rounded-full border border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800">
            <span class="text-label-md uppercase">Izin/Sakit:</span>
            <span class="text-headline-md" id="count-excused">0</span>
        </div>
        <div class="flex items-center gap-xs px-md py-xs bg-red-50 text-red-700 rounded-full border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
            <span class="text-label-md uppercase">Alpa:</span>
            <span class="text-headline-md" id="count-absent">0</span>
        </div>
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


<form action="<?php echo e(route('attendances.form')); ?>" method="GET" class="flex flex-wrap gap-md mb-lg">
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">JADWAL</label>
        <select name="schedule_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface focus:ring-primary focus:border-primary py-2 px-3 text-body-md" onchange="this.form.submit()">
            <?php $__currentLoopData = $schedules ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sched): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sched->id); ?>" <?php if(($schedule->id ?? null) == $sched->id): echo 'selected'; endif; ?>>
                <?php echo e($sched->teacherSubject->subject->name); ?> - <?php echo e($sched->teacherSubject->classroom->name); ?> (<?php echo e($sched->day); ?>, <?php echo e($sched->start_time); ?>-<?php echo e($sched->end_time); ?>)
            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="text-label-md text-on-surface-variant block mb-xs">TANGGAL</label>
        <input type="date" name="date" value="<?php echo e($date); ?>" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface focus:ring-primary focus:border-primary py-2 px-3 text-body-md">
    </div>
    <div class="self-end">
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','type' => 'submit','icon' => 'visibility']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit','icon' => 'visibility']); ?>Tampilkan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
    </div>
</form>


<form action="<?php echo e(route('attendances.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="schedule_id" value="<?php echo e($schedule->id ?? ''); ?>">
    <input type="hidden" name="date" value="<?php echo e($date); ?>">

    <div class="space-y-sm">
        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="student-card x-card p-sm flex flex-col sm:flex-row items-center gap-md transition-colors" data-student-id="<?php echo e($student->id); ?>">
            <div class="flex items-center gap-md w-full sm:w-auto">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-surface-container flex-shrink-0 flex items-center justify-center font-bold text-on-surface-variant">
                    <?php echo e(strtoupper(substr($student->name, 0, 2))); ?>

                </div>
                <div class="flex-grow">
                    <p class="text-headline-md text-on-surface leading-tight"><?php echo e($student->name); ?></p>
                    <p class="text-caption text-on-surface-variant">NIS: <?php echo e($student->nis); ?></p>
                </div>
            </div>
            <div class="flex w-full sm:w-auto bg-surface-container p-xs rounded-lg gap-xs">
                <input type="hidden" name="attendances[<?php echo e($loop->index); ?>][student_id]" value="<?php echo e($student->id); ?>">
                <?php $__currentLoopData = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="status-choice flex-1 sm:w-14 py-2 px-2 rounded-md text-label-md text-center text-on-surface-variant transition-colors hover:bg-surface-container-high cursor-pointer">
                    <input type="radio" name="attendances[<?php echo e($loop->index); ?>][status]" value="<?php echo e($key); ?>" class="hidden" <?php if($key === 'H'): echo 'checked'; endif; ?>>
                    <?php echo e($key); ?>

                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-xl text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl mb-md">people</span>
            <p>Pilih jadwal dan tanggal untuk menampilkan daftar siswa.</p>
        </div>
        <?php endif; ?>
    </div>

<?php if(count($students) > 0): ?>
    <?php if(!$canEdit): ?>
    <div class="mb-lg p-md bg-amber-50 text-amber-800 rounded-xl text-[14px] flex items-start gap-3 border border-amber-200">
        <span class="material-symbols-outlined text-[20px] mt-0.5 shrink-0">schedule</span>
        <div>
            <p class="font-semibold">Di luar jam operasional</p>
            <p class="mt-1">Absensi hanya dapat diisi dalam rentang blok sore (14:00 - 16:00 WIB).</p>
        </div>
    </div>
    <?php endif; ?>
    <div class="mt-lg">
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','size' => 'xl','type' => 'submit','icon' => 'send','iconPosition' => 'right','class' => 'w-full md:w-auto px-xl py-md','disabled' => !$canEdit]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','size' => 'xl','type' => 'submit','icon' => 'send','icon-position' => 'right','class' => 'w-full md:w-auto px-xl py-md','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$canEdit)]); ?>
            Submit Absensi
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
    </div>
<?php endif; ?>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const updateCounts = () => {
        const statuses = [...document.querySelectorAll('.student-card input[type="radio"]:checked')].map(input => input.value);
        document.getElementById('count-present').textContent = statuses.filter(status => status === 'H').length;
        document.getElementById('count-excused').textContent = statuses.filter(status => status === 'S' || status === 'I').length;
        document.getElementById('count-absent').textContent = statuses.filter(status => status === 'A').length;
    };

    document.querySelectorAll('.status-choice input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateCounts();
        });
    });
    updateCounts();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/attendances/form.blade.php ENDPATH**/ ?>