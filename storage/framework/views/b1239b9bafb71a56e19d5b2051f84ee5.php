<?php $__env->startSection('content'); ?>
<div class="mb-lg">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-md">
        <div>
            <span class="text-label-md text-primary uppercase tracking-widest mb-xs">LAPORAN RESMI</span>
            <h2 class="text-headline-xl mt-1">E-Rapor Preview</h2>
            <p class="text-on-surface-variant text-body-md mt-2">Laporan Capaian Akhir Semester</p>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 md:grid-cols-3 gap-lg mb-xl">
    
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['variant' => 'elevated','padding' => 'xl','class' => 'md:col-span-2 flex flex-col md:flex-row gap-xl items-start relative overflow-hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'elevated','padding' => 'xl','class' => 'md:col-span-2 flex flex-col md:flex-row gap-xl items-start relative overflow-hidden']); ?>
        <div class="w-32 h-32 rounded-lg bg-surface-container overflow-hidden border-2 border-primary/10 shrink-0 flex items-center justify-center text-4xl font-bold text-primary">
            <?php echo e(strtoupper(substr($student->name ?? 'A', 0, 2))); ?>

        </div>
        <div class="flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-xl gap-x-xl">
                <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1">Nama Siswa</p>
                    <p class="text-headline-md text-on-surface"><?php echo e($student->name ?? 'Ahmad Zulfikar'); ?></p>
                </div>
                <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1">NIS</p>
                    <p class="text-headline-md text-on-surface"><?php echo e($student->nis ?? '00452910384'); ?></p>
                </div>
                <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1">Kelas</p>
                    <p class="text-headline-md text-on-surface"><?php echo e($student->classroom->name ?? 'XII - IPA 1'); ?></p>
                </div>
                <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1">Semester</p>
                    <p class="text-headline-md text-on-surface"><?php echo e($semester ?? 'Ganjil'); ?> <?php echo e($academicYear ?? '2025/2026'); ?></p>
                </div>
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

    
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['variant' => 'elevated','padding' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'elevated','padding' => 'lg']); ?>
        <h3 class="text-headline-md mb-lg border-b border-outline-variant pb-sm">Rekap Absensi</h3>
        <div class="space-y-4">
            <?php
            $attStats = $attendanceStats ?? ['H' => 124, 'S' => 2, 'I' => 1, 'A' => 0];
            $colors = ['H' => ['bg-green-50', 'text-green-800', 'bg-green-100', 'text-green-700', '#065F46'],
                        'S' => ['bg-amber-50', 'text-amber-800', 'bg-amber-100', 'text-amber-700', '#92400E'],
                        'I' => ['bg-blue-50', 'text-blue-800', 'bg-blue-100', 'text-blue-700', '#1E40AF'],
                        'A' => ['bg-red-50', 'text-red-800', 'bg-red-100', 'text-red-700', '#991B1B']];
            ?>
            <?php $__currentLoopData = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between p-3 rounded-lg <?php echo e($colors[$key][0]); ?>">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full <?php echo e($colors[$key][2]); ?> <?php echo e($colors[$key][3]); ?> flex items-center justify-center font-bold text-xs"><?php echo e($key); ?></span>
                    <span class="text-body-md font-semibold"><?php echo e($label); ?></span>
                </div>
                <span class="text-headline-md" style="color: <?php echo e($colors[$key][4]); ?>"><?php echo e($attStats[$key]); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
</div>


<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['variant' => 'elevated','padding' => '0','class' => 'mb-xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'elevated','padding' => '0','class' => 'mb-xl']); ?>
    <div class="p-xl bg-surface-container-low flex justify-between items-center border-b border-outline-variant">
        <h3 class="text-headline-md">Capaian Akademik</h3>
        <span class="text-label-md bg-primary-container text-on-primary-container px-3 py-1 rounded-full">GPA: <?php echo e(number_format($gpa ?? 3.82, 2)); ?></span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="text-label-md text-on-surface-variant uppercase px-xl py-4">Mata Pelajaran</th>
                    <th class="text-label-md text-on-surface-variant uppercase px-xl py-4 text-center">KKM</th>
                    <th class="text-label-md text-on-surface-variant uppercase px-xl py-4 text-center">Nilai</th>
                    <th class="text-label-md text-on-surface-variant uppercase px-xl py-4 text-center">Grade</th>
                </tr>
            </thead>
            <tbody class="text-body-md">
                <?php $__empty_1 = true; $__currentLoopData = $grades ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-surface-container-low transition-colors border-b border-outline-variant">
                    <td class="px-xl py-4 font-semibold text-primary"><?php echo e($grade['subject']); ?></td>
                    <td class="px-xl py-4 text-center"><?php echo e($grade['kkm'] ?? 70); ?></td>
                    <td class="px-xl py-4 text-center font-bold"><?php echo e($grade['score']); ?></td>
                    <td class="px-xl py-4 text-center">
                        <?php
                        $s = $grade['score'] ?? 0;
                        $g = $s >= 90 ? 'A' : ($s >= 80 ? 'B' : ($s >= 70 ? 'C' : ($s >= 60 ? 'D' : 'E')));
                        $gc = ['A' => 'bg-green-100 text-green-800', 'B' => 'bg-blue-100 text-blue-800', 'C' => 'bg-amber-100 text-amber-800', 'D' => 'bg-orange-100 text-orange-800', 'E' => 'bg-red-100 text-red-800'];
                        ?>
                        <span class="px-3 py-1 rounded font-bold <?php echo e($gc[$g]); ?>"><?php echo e($g); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <?php $__currentLoopData = ['Bahasa Arab', 'Fisika', 'Biologi', 'Matematika Wajib', 'Bahasa Inggris', 'PAI', 'Kimia']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-surface-container-low transition-colors border-b border-outline-variant">
                    <td class="px-xl py-4 font-semibold text-primary"><?php echo e($s); ?></td>
                    <td class="px-xl py-4 text-center">75</td>
                    <td class="px-xl py-4 text-center font-bold"><?php echo e([92, 88, 84, 95, 89, 91, 78][$i]); ?></td>
                    <td class="px-xl py-4 text-center">
                        <?php $g = ['A','A','B','A','A','A','C'][$i]; ?>
                        <span class="px-3 py-1 rounded font-bold <?php echo e(['A' => 'bg-green-100 text-green-800', 'B' => 'bg-blue-100 text-blue-800', 'C' => 'bg-amber-100 text-amber-800'][$g]); ?>"><?php echo e($g); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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


<div class="flex flex-col md:flex-row items-center justify-between bg-primary p-xl rounded-xl text-white gap-lg shadow-lg shadow-primary/20">
    <div class="text-center md:text-left">
        <h4 class="text-headline-lg">Finalisasi E-Rapor</h4>
        <p class="text-on-primary-container/80 mt-1">Generate dokumen PDF resmi yang ditandatangani digital.</p>
    </div>
    <a href="<?php echo e(url('scores/rapor-pdf')); ?>?student_id=<?php echo e(request('student_id')); ?>&semester=<?php echo e(request('semester', 'ganjil')); ?>&academic_year=<?php echo e(request('academic_year', '2025/2026')); ?>" class="bg-surface-container-lowest text-primary px-8 py-4 rounded-xl font-bold flex items-center gap-3 hover:scale-105 active:scale-95 transition-all shadow-md">
        <span class="material-symbols-outlined">picture_as_pdf</span>
        Ekspor PDF Resmi
    </a>
</div>

<div class="mt-xl mb-20 text-center">
    <p class="text-on-surface-variant text-caption italic">
        Laporan digenerate pada: <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?> pukul <?php echo e(now()->format('H:i')); ?> WIB.<br>
        Terautentikasi secara digital oleh Madani-SMS.
    </p>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/scores/rapor-preview.blade.php ENDPATH**/ ?>