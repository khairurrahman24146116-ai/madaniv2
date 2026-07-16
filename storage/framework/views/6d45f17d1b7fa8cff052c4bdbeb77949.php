<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Jadwal Hari Ini','subtitle' => 'Tampilan mobile untuk guru','icon' => 'calendar_month','actions' => [
        ['type' => 'button', 'label' => 'Desktop', 'icon' => 'desktop_mac', 'variant' => 'secondary', 'href' => route('schedules.index')],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Jadwal Hari Ini','subtitle' => 'Tampilan mobile untuk guru','icon' => 'calendar_month','actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['type' => 'button', 'label' => 'Desktop', 'icon' => 'desktop_mac', 'variant' => 'secondary', 'href' => route('schedules.index')],
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

<div class="flex flex-wrap gap-sm mb-lg">
    <?php $__currentLoopData = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('schedules.mobile', ['day' => $d])); ?>" class="px-4 py-2 rounded-lg text-label-md font-medium transition-colors <?php if($d === $currentDay): ?> bg-primary text-on-primary <?php else: ?> bg-surface-container-high text-on-surface-variant <?php endif; ?>">
            <?php echo e(ucfirst($d)); ?>

        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="space-y-md">
    <?php $__empty_1 = true; $__currentLoopData = $schedules ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php if (isset($component)) { $__componentOriginalcf900cf5e65b6e5c79c673e777bb4f83 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcf900cf5e65b6e5c79c673e777bb4f83 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.schedule-card','data' => ['schedule' => $schedule,'variant' => 'mobile']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('schedule-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['schedule' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($schedule),'variant' => 'mobile']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcf900cf5e65b6e5c79c673e777bb4f83)): ?>
<?php $attributes = $__attributesOriginalcf900cf5e65b6e5c79c673e777bb4f83; ?>
<?php unset($__attributesOriginalcf900cf5e65b6e5c79c673e777bb4f83); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcf900cf5e65b6e5c79c673e777bb4f83)): ?>
<?php $component = $__componentOriginalcf900cf5e65b6e5c79c673e777bb4f83; ?>
<?php unset($__componentOriginalcf900cf5e65b6e5c79c673e777bb4f83); ?>
<?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['variant' => 'elevated','padding' => 'md','class' => 'bg-primary text-white text-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'elevated','padding' => 'md','class' => 'bg-primary text-white text-center']); ?>
            <h4 class="text-headline-md mb-xs">Tidak ada jadwal pada hari ini</h4>
            <p class="text-body-md opacity-80">Jadwal sesi besok akan muncul di sini.</p>
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
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/schedules/mobile.blade.php ENDPATH**/ ?>