<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Input Nilai','subtitle' => 'Input dan kelola nilai akademik harian siswa','icon' => 'grade','actions' => [
        ['type' => 'button', 'label' => 'Import Excel', 'icon' => 'upload_file', 'variant' => 'outline', 'onclick' => 'openImportModal()'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Input Nilai','subtitle' => 'Input dan kelola nilai akademik harian siswa','icon' => 'grade','actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['type' => 'button', 'label' => 'Import Excel', 'icon' => 'upload_file', 'variant' => 'outline', 'onclick' => 'openImportModal()'],
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


<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter mb-lg">
    <form method="GET" action="<?php echo e(route('scores.create')); ?>" class="md:col-span-8 x-card p-md">
        <label class="block text-label-md text-on-surface-variant mb-xs" for="teacher_subject_id">MATA PELAJARAN DAN KELAS</label>
        <select id="teacher_subject_id" name="teacher_subject_id" onchange="this.form.submit()" class="w-full rounded-lg border-outline-variant bg-surface-bright text-on-surface focus:ring-primary focus:border-primary py-2 px-3 text-body-md">
            <?php $__empty_1 = true; $__currentLoopData = $teacherSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mapping): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <option value="<?php echo e($mapping->id); ?>" <?php if($selectedMapping?->id === $mapping->id): echo 'selected'; endif; ?>>
                <?php echo e($mapping->subject->name); ?> - <?php echo e($mapping->classroom->name); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <option>Tidak ada kelas yang dapat diinput</option>
            <?php endif; ?>
        </select>
    </form>
    <div class="md:col-span-4 bg-primary-container text-on-primary-container rounded-xl p-md flex flex-col justify-between">
        <div>
            <h3 class="text-label-md opacity-80 uppercase tracking-wider">Bobot Nilai Saat Ini</h3>
            <div class="mt-xs flex items-baseline gap-xs">
                <span class="text-[24px] font-bold"><?php echo e(count($students)); ?></span>
                <span class="text-xs opacity-70">SISWA AKTIF</span>
            </div>
        </div>
    </div>
</div>


<div class="mb-lg flex flex-col sm:flex-row sm:items-center justify-between gap-md">
    <div class="inline-flex bg-surface-container-high p-1 rounded-xl shadow-inner border border-outline-variant">
        <?php $__currentLoopData = ['tugas' => 'Tugas', 'ph' => 'Harian', 'uts' => 'UTS', 'uas' => 'UAS']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button type="button" data-component="<?php echo e($code); ?>" class="score-component px-6 py-2 rounded-lg text-label-md <?php if($loop->first): ?> bg-primary text-on-primary shadow-sm <?php else: ?> text-on-surface-variant hover:bg-surface-container <?php endif; ?> transition-all"><?php echo e($label); ?></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['variant' => 'default','class' => 'overflow-x-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'default','class' => 'overflow-x-auto']); ?>
    <table class="w-full min-w-[720px] text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
                <th class="px-md py-4 text-label-md text-on-surface-variant">SISWA</th>
                <th class="px-md py-4 text-label-md text-on-surface-variant text-center w-24">NIS</th>
                <th class="px-md py-4 text-label-md text-on-surface-variant text-center w-32">NILAI (0-100)</th>
                <th class="px-md py-4 text-label-md text-on-surface-variant text-center w-32">STATUS</th>
                <th class="px-md py-4 text-label-md text-on-surface-variant text-center w-20">NA*</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            <?php $__currentLoopData = $students ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="px-md py-4">
                    <div class="flex items-center gap-md">
                        <div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold"><?php echo e(strtoupper(substr($student->name, 0, 1))); ?></div>
                        <div>
                            <p class="text-body-lg text-on-surface font-semibold"><?php echo e($student->name); ?></p>
                            <p class="text-xs text-on-surface-variant"><?php echo e($student->gender === 'L' ? 'Laki-laki' : 'Perempuan'); ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-md py-4 text-center text-body-md text-on-surface-variant"><?php echo e($student->nis); ?></td>
                <td class="px-md py-4">
                    <input class="w-full text-center rounded-lg border-outline-variant bg-surface-bright focus:ring-primary focus:border-primary py-2 text-body-md score-input" data-student-id="<?php echo e($student->id); ?>" max="100" min="0" placeholder="Nilai" type="number">
                </td>
                <td class="px-md py-4 text-center">
                    <span class="px-3 py-1 bg-surface-container text-on-surface-variant rounded-full text-xs font-semibold">BELUM</span>
                </td>
                <td class="px-md py-4 text-center font-bold text-on-surface-variant">-</td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
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


<div class="mt-lg grid grid-cols-1 md:grid-cols-12 gap-gutter items-end">
    <div class="md:col-span-8 bg-surface-container-low rounded-lg p-md border border-outline-variant flex flex-wrap gap-xl">
        <div>
            <p class="text-caption text-on-surface-variant">RATA-RATA KELAS</p>
            <p class="text-headline-md text-primary" id="avg-score">-</p>
        </div>
        <div class="w-px h-10 bg-outline-variant self-center"></div>
        <div>
            <p class="text-caption text-on-surface-variant">TERTINGGI / TERENDAH</p>
            <p class="text-headline-md text-on-surface" id="range-score">-</p>
        </div>
        <div class="w-px h-10 bg-outline-variant self-center"></div>
        <div>
            <p class="text-caption text-on-surface-variant">KETUNTASAN</p>
            <p class="text-headline-md text-green-700" id="pass-rate">-</p>
        </div>
    </div>
    <div class="md:col-span-4">
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['id' => 'save-scores','variant' => 'primary','size' => 'xl','type' => 'button','icon' => 'save','iconPosition' => 'left','class' => 'w-full','disabled' => !$selectedMapping || count($students) === 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'save-scores','variant' => 'primary','size' => 'xl','type' => 'button','icon' => 'save','icon-position' => 'left','class' => 'w-full','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$selectedMapping || count($students) === 0)]); ?>
            Simpan Nilai
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
        <p id="score-feedback" class="text-center text-xs text-on-surface-variant mt-sm" role="status">Masukkan nilai, lalu simpan komponen yang dipilih.</p>
    </div>
</div>


<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'import-modal','title' => 'Import Nilai dari Excel','size' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'import-modal','title' => 'Import Nilai dari Excel','size' => 'md']); ?>
    <form id="import-form" class="space-y-4">
        <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'file','name' => 'excel_file','label' => 'File Excel (.xlsx)','required' => true,'hint' => 'Format: NIS, Nilai (header: nis,value)','accept' => '.xlsx,.xls']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'file','name' => 'excel_file','label' => 'File Excel (.xlsx)','required' => true,'hint' => 'Format: NIS, Nilai (header: nis,value)','accept' => '.xlsx,.xls']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'hidden','name' => 'component_code','id' => 'import-component','value' => 'tugas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'hidden','name' => 'component_code','id' => 'import-component','value' => 'tugas']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'hidden','name' => 'teacher_subject_id','id' => 'import-ts','value' => ''.e($selectedMapping?->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'hidden','name' => 'teacher_subject_id','id' => 'import-ts','value' => ''.e($selectedMapping?->id).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'hidden','name' => 'semester','value' => 'ganjil']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'hidden','name' => 'semester','value' => 'ganjil']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'hidden','name' => 'academic_year','value' => '2025/2026']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'hidden','name' => 'academic_year','value' => '2025/2026']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
        <div class="flex justify-end gap-sm pt-4">
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','type' => 'button','onclick' => 'closeModal(\'import-modal\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','type' => 'button','onclick' => 'closeModal(\'import-modal\')']); ?>Batal <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','type' => 'submit','icon' => 'upload_file']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit','icon' => 'upload_file']); ?>Import <?php echo $__env->renderComponent(); ?>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.querySelectorAll('.score-input').forEach(input => {
        input.addEventListener('input', function() {
            let val = parseInt(this.value);
            if (val > 100) this.value = 100;
            if (val < 0 || isNaN(val)) this.value = 0;
            updateStats();
        });
    });

    function updateStats() {
        const inputs = document.querySelectorAll('.score-input');
        let values = [];
        inputs.forEach(inp => {
            let v = parseInt(inp.value);
            if (!isNaN(v) && v > 0) values.push(v);
        });
        if (values.length === 0) {
            document.getElementById('avg-score').textContent = '-';
            document.getElementById('range-score').textContent = '-';
            document.getElementById('pass-rate').textContent = '-';
            return;
        }
        const sum = values.reduce((a, b) => a + b, 0);
        const avg = (sum / values.length).toFixed(1);
        const max = Math.max(...values);
        const min = Math.min(...values);
        const pass = values.filter(v => v >= 75).length;
        const rate = ((pass / values.length) * 100).toFixed(0);
        document.getElementById('avg-score').textContent = avg;
        document.getElementById('range-score').textContent = max + ' / ' + min;
        document.getElementById('pass-rate').textContent = rate + '%';
    }

    let component = 'tugas';
    const subjectId = <?php echo e($selectedMapping?->subject_id ?? 'null'); ?>;
    const saveButton = document.getElementById('save-scores');
    const feedback = document.getElementById('score-feedback');

    document.querySelectorAll('.score-component').forEach(button => {
        button.addEventListener('click', () => {
            component = button.dataset.component;
            document.querySelectorAll('.score-component').forEach(item => {
                item.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
                item.classList.add('text-on-surface-variant');
            });
            button.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
            button.classList.remove('text-on-surface-variant');
        });
    });

    saveButton?.addEventListener('click', async () => {
        const scores = [...document.querySelectorAll('.score-input')]
            .filter(input => input.value !== '')
            .map(input => ({ student_id: Number(input.dataset.studentId), value: Number(input.value) }));

        if (!scores.length) {
            feedback.textContent = 'Masukkan setidaknya satu nilai sebelum menyimpan.';
            feedback.className = 'text-center text-xs text-error mt-sm';
            return;
        }

        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Menyimpan...';
        try {
            const response = await fetch('<?php echo e(url('/scores/batch')); ?>', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    subject_id: subjectId,
                    component_code: component,
                    semester: 'ganjil',
                    academic_year: '2025/2026',
                    scores,
                }),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Nilai gagal disimpan.');

            feedback.textContent = result.message;
            feedback.className = 'text-center text-xs text-green-700 mt-sm';
            saveButton.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Nilai Tersimpan';
        } catch (error) {
            feedback.textContent = error.message;
            feedback.className = 'text-center text-xs text-error mt-sm';
            saveButton.innerHTML = '<span class="material-symbols-outlined">save</span> Simpan Nilai';
        } finally {
            saveButton.disabled = false;
        }
    });

    function openImportModal() {
        document.getElementById('import-modal').classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/scores/create.blade.php ENDPATH**/ ?>