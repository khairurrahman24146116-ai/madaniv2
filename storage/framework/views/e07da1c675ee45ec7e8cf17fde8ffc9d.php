<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'modal',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl, full
    'closeable' => true,
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
    'id' => 'modal',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl, full
    'closeable' => true,
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
    $sizes = [
        'sm' => 'max-w-[400px]',
        'md' => 'max-w-[500px]',
        'lg' => 'max-w-[640px]',
        'xl' => 'max-w-[800px]',
        '2xl' => 'max-w-2xl',
        'full' => 'max-w-4xl',
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
?>

<div id="<?php echo e($id); ?>" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="<?php echo e($id); ?>-title">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="$dispatch('close-modal', { id: '<?php echo e($id); ?>' })"></div>
    
    <!-- Modal Container -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="w-full <?php echo e($sizeClass); ?> bg-surface-container-lowest rounded-xl shadow-xl border border-outline-variant dark:border-outline dark:shadow-none dark:ring-1 dark:ring-primary/20 transform transition-all <?php echo e($class); ?>">
            <?php if($title || $closeable): ?>
                <div class="flex items-center justify-between p-md border-b border-outline-variant">
                    <?php if($title): ?>
                        <h2 id="<?php echo e($id); ?>-title" class="text-headline-md font-semibold text-on-surface"><?php echo e($title); ?></h2>
                    <?php endif; ?>
                    <?php if($closeable): ?>
                        <button type="button"
                                class="p-1.5 rounded-lg hover:bg-surface-container-high transition-colors text-on-surface-variant"
                                @click="$dispatch('close-modal', { id: '<?php echo e($id); ?>' })"
                                aria-label="Tutup">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="p-md">
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Alpine.js modal helper
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('<?php echo e($id); ?>');
        if (!modal) return;
        
        window.openModal = window.openModal || function(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('hidden');
        };
        
        window.closeModal = window.closeModal || function(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        };
        
        modal.addEventListener('close-modal', (e) => {
            if (e.detail.id === '<?php echo e($id); ?>') {
                modal.classList.add('hidden');
            }
        });
        
        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\laragon\www\madani-al-aziziyah\resources\views/components/modal.blade.php ENDPATH**/ ?>