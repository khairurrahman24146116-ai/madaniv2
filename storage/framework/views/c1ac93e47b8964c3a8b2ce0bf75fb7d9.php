<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'text', // text, email, password, number, tel, url, search, date, time, datetime-local, month, week, select, textarea, checkbox, radio, file
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'hint' => null,
    'class' => '',
    'options' => [], // for select
    'multiple' => false, // for select
    'rows' => 3, // for textarea
    'min' => null,
    'max' => null,
    'step' => null,
    'autocomplete' => null,
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
    'type' => 'text', // text, email, password, number, tel, url, search, date, time, datetime-local, month, week, select, textarea, checkbox, radio, file
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'hint' => null,
    'class' => '',
    'options' => [], // for select
    'multiple' => false, // for select
    'rows' => 3, // for textarea
    'min' => null,
    'max' => null,
    'step' => null,
    'autocomplete' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $id = $id ?? $name ?? 'input-' . uniqid();
    $inputId = $id;
    $labelId = $id . '-label';
    $errorId = $id . '-error';
    $hintId = $id . '-hint';
    
    $describedBy = [];
    if ($error) $describedBy[] = $errorId;
    if ($hint) $describedBy[] = $hintId;
    $ariaDescribedBy = $describedBy ? 'aria-describedby="' . implode(' ', $describedBy) . '"' : '';
?>

<div class="w-full <?php echo e($class); ?>">
    <?php if($label): ?>
        <label for="<?php echo e($inputId); ?>" id="<?php echo e($labelId); ?>" class="block text-label-md text-on-surface-variant mb-xs">
            <?php echo e($label); ?>

            <?php if($required): ?>
                <span class="text-error ml-0.5" aria-hidden="true">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    
    <?php if($type === 'select'): ?>
        <select name="<?php echo e($name); ?>"
                id="<?php echo e($inputId); ?>"
                class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed <?php echo e($error ? 'border-error focus:ring-error/20' : ''); ?>"
                <?php if($disabled): ?> disabled <?php endif; ?>
                <?php if($required): ?> required <?php endif; ?>
                <?php if($multiple): ?> multiple <?php endif; ?>
                <?php echo e($ariaDescribedBy); ?>>
            <?php if($placeholder): ?>
                <option value="" disabled selected><?php echo e($placeholder); ?></option>
            <?php endif; ?>
            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>" <?php echo e((string)$value === (string)($value ?? old($name)) ? 'selected' : ''); ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    <?php elseif($type === 'textarea'): ?>
        <textarea name="<?php echo e($name); ?>"
                  id="<?php echo e($inputId); ?>"
                  rows="<?php echo e($rows); ?>"
                  class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed resize-y <?php echo e($error ? 'border-error focus:ring-error/20' : ''); ?>"
                  <?php if($disabled): ?> disabled <?php endif; ?>
                  <?php if($required): ?> required <?php endif; ?>
                  <?php if($placeholder): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
                  <?php echo e($ariaDescribedBy); ?>><?php echo e(old($name, $value)); ?></textarea>
    <?php elseif($type === 'checkbox'): ?>
        <div class="flex items-start gap-3">
            <input type="checkbox"
                   name="<?php echo e($name); ?>"
                   id="<?php echo e($inputId); ?>"
                   value="1"
                   class="mt-0.5 w-4 h-4 rounded border-outline-variant text-primary focus:ring-2 focus:ring-primary/20 disabled:opacity-50 disabled:cursor-not-allowed <?php echo e($error ? 'border-error' : ''); ?>"
                   <?php if($disabled): ?> disabled <?php endif; ?>
                   <?php if($required): ?> required <?php endif; ?>
                   <?php if(old($name, $value)): ?> checked <?php endif; ?>
                   <?php echo e($ariaDescribedBy); ?>>
            <label for="<?php echo e($inputId); ?>" class="text-body-md text-on-surface cursor-pointer">
                <?php echo e($label ?? $slot); ?>

            </label>
        </div>
    <?php elseif($type === 'radio'): ?>
        <div class="flex items-center gap-3">
            <input type="radio"
                   name="<?php echo e($name); ?>"
                   id="<?php echo e($inputId); ?>"
                   value="<?php echo e($value); ?>"
                   class="w-4 h-4 border-outline-variant text-primary focus:ring-2 focus:ring-primary/20 disabled:opacity-50 disabled:cursor-not-allowed <?php echo e($error ? 'border-error' : ''); ?>"
                   <?php if($disabled): ?> disabled <?php endif; ?>
                   <?php if($required): ?> required <?php endif; ?>
                   <?php if(old($name) == $value || $value === old($name)): ?> checked <?php endif; ?>
                   <?php echo e($ariaDescribedBy); ?>>
            <label for="<?php echo e($inputId); ?>" class="text-body-md text-on-surface cursor-pointer">
                <?php echo e($label ?? $slot); ?>

            </label>
        </div>
    <?php elseif($type === 'file'): ?>
        <input type="file"
               name="<?php echo e($name); ?>"
               id="<?php echo e($inputId); ?>"
               class="w-full text-body-md text-on-surface-variant file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-label-md file:font-semibold file:bg-primary-container file:text-on-primary-container hover:file:bg-primary/80 transition-colors disabled:opacity-50 disabled:cursor-not-allowed <?php echo e($error ? 'border-error' : ''); ?>"
               <?php if($disabled): ?> disabled <?php endif; ?>
               <?php if($required): ?> required <?php endif; ?>
               <?php if($multiple): ?> multiple <?php endif; ?>
               <?php echo e($ariaDescribedBy); ?>>
    <?php else: ?>
        <input type="<?php echo e($type); ?>"
               name="<?php echo e($name); ?>"
               id="<?php echo e($inputId); ?>"
               value="<?php echo e(old($name, $value)); ?>"
               placeholder="<?php echo e($placeholder); ?>"
               class="w-full rounded-lg border border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed <?php echo e($error ? 'border-error focus:ring-error/20' : ''); ?>"
               <?php if($disabled): ?> disabled <?php endif; ?>
               <?php if($readonly): ?> readonly <?php endif; ?>
               <?php if($required): ?> required <?php endif; ?>
               <?php if($min !== null): ?> min="<?php echo e($min); ?>" <?php endif; ?>
               <?php if($max !== null): ?> max="<?php echo e($max); ?>" <?php endif; ?>
               <?php if($step !== null): ?> step="<?php echo e($step); ?>" <?php endif; ?>
               <?php if($autocomplete): ?> autocomplete="<?php echo e($autocomplete); ?>" <?php endif; ?>
               <?php echo e($ariaDescribedBy); ?>>
    <?php endif; ?>
    
    <?php if($error): ?>
        <p id="<?php echo e($errorId); ?>" class="mt-xs text-caption text-error flex items-center gap-1" role="alert">
            <span class="material-symbols-outlined text-[14px]">error_outline</span>
            <?php echo e($error); ?>

        </p>
    <?php elseif($hint): ?>
        <p id="<?php echo e($hintId); ?>" class="mt-xs text-caption text-on-surface-variant flex items-center gap-1">
            <?php echo e($hint); ?>

        </p>
    <?php endif; ?>
</div><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/components/form-input.blade.php ENDPATH**/ ?>