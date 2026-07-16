<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'default', // default, elevated, outlined, filled
    'padding' => 'md', // none, sm, md, lg, xl
    'hover' => false,
    'class' => '',
    'borderLeft' => null, // color class for left border accent
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
    'variant' => 'default', // default, elevated, outlined, filled
    'padding' => 'md', // none, sm, md, lg, xl
    'hover' => false,
    'class' => '',
    'borderLeft' => null, // color class for left border accent
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $variants = [
        'default' => 'bg-surface-container-lowest border border-outline-variant',
        'elevated' => 'bg-surface-container-low shadow-sm',
        'outlined' => 'bg-surface-container-lowest border-2 border-outline',
        'filled' => 'bg-surface-container-high',
    ];
    
    $paddings = [
        'none' => '',
        'sm' => 'p-sm',
        'md' => 'p-md',
        'lg' => 'p-lg',
        'xl' => 'p-xl',
    ];
    
    $hoverClass = $hover ? 'hover:bg-surface-container-low dark:hover:bg-surface-container transition-colors' : '';
    $variantClass = $variants[$variant] ?? $variants['default'];
    $paddingClass = $paddings[$padding] ?? $paddings['md'];
    $borderLeftClass = $borderLeft ? "border-l-4 {$borderLeft}" : '';
    
    // Dark mode adjustments
    $darkAdjustments = 'dark:border-outline dark:shadow-none';
    if ($variant === 'default') {
        $darkAdjustments .= ' dark:border-t-2 dark:border-primary';
    }
?>

<div class="rounded-lg <?php echo e($variantClass); ?> <?php echo e($paddingClass); ?> <?php echo e($hoverClass); ?> <?php echo e($borderLeftClass); ?> <?php echo e($darkAdjustments); ?> <?php echo e($class); ?>">
    <?php echo e($slot); ?>

</div><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/components/card.blade.php ENDPATH**/ ?>