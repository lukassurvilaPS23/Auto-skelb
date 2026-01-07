

<?php $__env->startSection('content'); ?>
<h1>Auto renginiai</h1>
<p class="muted">Frontend: Blade + HTML+JS. Duomenys imami iš REST API.</p>

<div class="card">
    <h3>Greitos nuorodos</h3>
    <ul>
        <li><a href="<?php echo e(route('renginiai')); ?>">Renginių sąrašas</a></li>
        <li><a href="<?php echo e(route('prisijungti')); ?>">Prisijungti</a></li>
        <li><a href="<?php echo e(route('registruotis')); ?>">Registruotis</a></li>
        <li><a href="<?php echo e(route('xml')); ?>">XML eksportas</a></li>
    </ul>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\AUTOSKELB_praktika\auto-renginiai\resources\views/puslapiai/pagrindinis.blade.php ENDPATH**/ ?>