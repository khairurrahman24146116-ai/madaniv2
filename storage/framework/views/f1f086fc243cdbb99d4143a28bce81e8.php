<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary', // primary, secondary, outline, ghost, error
    'size' => 'md', // sm, md, lg, xl
    'type' => 'button', // button, submit, reset
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'fullWidth' => false,
    'class' => '',
    'href' => null, // if set, renders as <a> tag
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
    'variant' => 'primary', // primary, secondary, outline, ghost, error
    'size' => 'md', // sm, md, lg, xl
    'type' => 'button', // button, submit, reset
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'fullWidth' => false,
    'class' => '',
    'href' => null, // if set, renders as <a> tag
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
        'primary' => 'bg-primary text-on-primary hover:bg-primary/90 active:bg-primary/80 shadow-sm',
        'secondary' => 'bg-secondary-container text-on-secondary-container hover:bg-secondary-container/90',
        'outline' => 'border border-outline bg-transparent hover:bg-surface-container-high',
        'ghost' => 'bg-transparent hover:bg-surface-container-high',
        'error' => 'bg-error text-on-error hover:bg-error/90',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-label-md gap-1.5',
        'md' => 'px-4 py-2 text-body-md gap-2',
        'lg' => 'px-6 py-2.5 text-body-lg gap-2',
        'xl' => 'px-8 py-3 text-headline-md gap-2.5',
    ];
    
    $iconSizes = [
        'sm' => 'text-[16px]',
        'md' => 'text-[18px]',
        'lg' => 'text-[20px]',
        'xl' => 'text-[22px]',
    ];
    
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed';
    $widthClass = $fullWidth ? 'w-full' : '';
    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
?>

<?php if($href): ?>
    <a href="<?php echo e($href); ?>"
       class="<?php echo e($baseClasses); ?> <?php echo e($variantClass); ?> <?php echo e($sizeClass); ?> <?php echo e($widthClass); ?> <?php echo e($class); ?>"
       <?php if($disabled): ?> tabindex="-1" aria-disabled="true" <?php endif; ?>>
        <?php if($icon && $iconPosition === 'left'): ?>
            <span class="material-symbols-outlined <?php echo e($iconClass); ?>"><?php echo e($icon); ?></span>
        <?php endif; ?>
        <?php echo e($slot); ?>

        <?php if($icon && $iconPosition === 'right'): ?>
            <span class="material-symbols-outlined <?php echo e($iconClass); ?>"><?php echo e($icon); ?></span>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button type="<?php echo e($type); ?>"
            class="<?php echo e($baseClasses); ?> <?php echo e($variantClass); ?> <?php echo e($sizeClass); ?> <?php echo e($widthClass); ?> <?php echo e($class); ?>"
            <?php if($disabled): ?> disabled <?php endif; ?>>
        <?php if($icon && $iconPosition === 'left'): ?>
            <span class="material-symbols-outlined <?php echo e($iconClass); ?>"><?php echo e($icon); ?></span>
        <?php endif; ?>
        <?php echo e($slot); ?>

        <?php if($icon && $iconPosition === 'right'): ?>
            <span class="material-symbols-outlined <?php echo e($iconClass); ?>"><?php echo e($icon); ?></span>
        <?php endif; ?>
    </button>
<?php endif; ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/components/button.blade.php ENDPATH**/ ?>