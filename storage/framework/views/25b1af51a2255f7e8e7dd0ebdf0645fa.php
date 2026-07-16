<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['href', 'icon', 'active' => false]));

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

foreach (array_filter((['href', 'icon', 'active' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<a href="<?php echo e($href); ?>"
   class="flex items-center gap-md p-md mx-sm my-xs rounded-lg transition-all <?php if($active): ?> bg-secondary-container text-on-secondary-container <?php else: ?> text-on-surface-variant hover:bg-surface-container-high <?php endif; ?>">
    <span class="material-symbols-outlined"><?php echo e($icon); ?></span>
    <span class="text-label-md"><?php echo e($slot); ?></span>
</a>
<?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/components/nav-item.blade.php ENDPATH**/ ?>