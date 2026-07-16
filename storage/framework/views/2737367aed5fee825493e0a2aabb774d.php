<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'schedule' => [],
    'variant' => 'default', // default, compact
    'showActions' => false,
    'class' => '',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'schedule' => [],
    'variant' => 'default', // default, compact
    'showActions' => false,
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $schedule = is_array($schedule) ? (object)$schedule : $schedule;
    $time = $schedule->time ?? ($schedule->start_time . ' - ' . $schedule->end_time);
    $subject = $schedule->subject ?? 'Mata Pelajaran';
    $class = $schedule->class ?? $schedule->classroom ?? 'Kelas';
    $type = $schedule->type ?? 'TEORI';
    $teacher = $schedule->teacher ?? '';
    $room = $schedule->room ?? '';
    
    $typeColors = [
        'TEORI' => 'bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
        'PRAKTIKUM' => 'bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-300',
        'UTS' => 'bg-amber-50 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300',
        'UAS' => 'bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300',
        'REMEDIAL' => 'bg-purple-50 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300',
    ];
    
    $typeClass = $typeColors[$type] ?? 'bg-surface-container-high text-on-surface-variant';
?>

<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['variant' => 'default','padding' => 'md','class' => 'relative overflow-hidden hover:bg-surface-container-low transition-colors '.e($class).'','style' => 'border-left: 4px solid var(--color-primary);']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'default','padding' => 'md','class' => 'relative overflow-hidden hover:bg-surface-container-low transition-colors '.e($class).'','style' => 'border-left: 4px solid var(--color-primary);']); ?>
    <div class="flex justify-between items-start mb-sm">
        <span class="text-label-md text-primary font-bold"><?php echo e($time); ?></span>
        <span class="px-sm py-[2px] <?php echo e($typeClass); ?> rounded text-[10px] font-bold"><?php echo e($type); ?></span>
    </div>
    <h4 class="text-headline-md text-on-surface mb-xs"><?php echo e($subject); ?></h4>
    <p class="text-body-md text-on-surface-variant mb-md"><?php echo e($class); ?></p>
    
    <?php if($teacher || $room): ?>
        <div class="flex flex-wrap gap-sm text-caption text-on-surface-variant">
            <?php if($teacher): ?>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">person</span>
                    <?php echo e($teacher); ?>

                </span>
            <?php endif; ?>
            <?php if($room): ?>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">meeting_room</span>
                    <?php echo e($room); ?>

                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if($showActions): ?>
        <div class="mt-md pt-md border-t border-outline-variant flex gap-sm">
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','size' => 'sm','icon' => 'visibility']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','size' => 'sm','icon' => 'visibility']); ?>Lihat <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php if($showActions === 'teacher'): ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','size' => 'sm','icon' => 'how_to_reg','href' => ''.e(route('attendances.form', ['schedule_id' => $schedule->id ?? ''])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','size' => 'sm','icon' => 'how_to_reg','href' => ''.e(route('attendances.form', ['schedule_id' => $schedule->id ?? ''])).'']); ?>Absensi <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/components/schedule-card.blade.php ENDPATH**/ ?>