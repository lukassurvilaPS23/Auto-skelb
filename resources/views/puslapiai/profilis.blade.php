@extends('layouts.app')

@section('content')
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
@endsection
