

<?php $__env->startSection('content'); ?>
<h1>Profilis</h1>

<div class="card">
  <p class="muted">Jei neprisijungęs – matysi 401.</p>
  <pre id="out">Kraunama...</pre>
</div>

<script>
(async () => {
  const out = document.getElementById('out');
  const { ok, status, payload } = await Api.request('GET', '/api/as');
  if (!ok) { out.textContent = `Neprisijungęs (status ${status}).`; UI.setAuthNav(false); return; }
  out.textContent = JSON.stringify(payload, null, 2);
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\AUTOSKELB_praktika\auto-renginiai\resources\views/puslapiai/profilis.blade.php ENDPATH**/ ?>