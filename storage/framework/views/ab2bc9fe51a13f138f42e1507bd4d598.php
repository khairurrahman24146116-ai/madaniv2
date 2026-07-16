<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'actions' => [],
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
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'actions' => [],
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="mb-6 <?php echo e($class); ?>">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <?php if($icon): ?>
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-primary"><?php echo e($icon); ?></span>
                    <h2 class="text-2xl font-bold text-on-surface tracking-tight"><?php echo e($title); ?></h2>
                </div>
            <?php else: ?>
                <h2 class="text-2xl font-bold text-on-surface tracking-tight"><?php echo e($title); ?></h2>
            <?php endif; ?>
            <?php if($subtitle): ?>
                <p class="text-on-surface-variant text-sm mt-1"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </div>
        <?php if(!empty($actions)): ?>
            <div class="flex flex-wrap gap-2 self-end">
                <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $aVariant = $action['variant'] ?? 'primary';
                        $aSize = $action['size'] ?? 'md';
                        $aIcon = $action['icon'] ?? null;
                        $aHref = $action['href'] ?? null;
                        $aDisabled = $action['disabled'] ?? false;
                    ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => $aVariant,'size' => $aSize,'icon' => $aIcon,'href' => $aHref,'disabled' => $aDisabled]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($aVariant),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($aSize),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($aIcon),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($aHref),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($aDisabled)]); ?>
                        <?php echo e($action['label']); ?>

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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/components/page-header.blade.php ENDPATH**/ ?>