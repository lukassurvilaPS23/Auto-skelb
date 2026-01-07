@extends('layouts.app')

@section('content')
<h1>Auto renginiai</h1>

<div class="card">
    <div class="row">
        <div style="flex:1; min-width:240px;">
            <label>Miestas</label>
            <input id="miestas" placeholder="Pvz. Vilnius">
        </div>
        <div style="flex:1; min-width:240px;">
            <label>Statusas</label>
            <input id="statusas" placeholder="Pvz. aktyvus">
        </div>
        <div style="align-self:end;">
            <button class="btn" id="filtruotiBtn" type="button">Filtruoti</button>
        </div>
    </div>
</div>

<div id="out" class="card">Kraunama...</div>

<script>
(async () => {
  const out = document.getElementById('out');

  async function load() {
    out.textContent = 'Kraunama...';

    const miestas = document.getElementById('miestas').value.trim();
    const statusas = document.getElementById('statusas').value.trim();

    const qs = new URLSearchParams();
    if (miestas) qs.set('miestas', miestas);
    if (statusas) qs.set('statusas', statusas);

    const url = '/api/auto-renginiai' + (qs.toString() ? `?${qs}` : '');
    const { ok, status, payload } = await Api.request('GET', url);

    if (!ok) { out.textContent = `Klaida (status ${status})`; return; }

    const list = payload.auto_renginiai || [];
    if (list.length === 0) { out.textContent = 'Renginių nėra.'; return; }

    out.innerHTML = '<ul>' + list.map(r => `
      <li>
        <a href="/renginiai/${r.id}">${r.pavadinimas}</a>
        — ${r.miestas} — ${r.pradzios_data}
      </li>
    `).join('') + '</ul>';
  }

  document.getElementById('filtruotiBtn').addEventListener('click', load);
  await load();
})();
</script>
@endsection