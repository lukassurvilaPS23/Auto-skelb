<template>
  <div>
    <h1>Mano renginiai</h1>
    <p class="muted">Šis puslapis veiks, jei esi prisijungęs ir turi teises kurti/valdyti renginius.</p>
    <div class="card">
      <h3>Sukurti / atnaujinti renginį</h3>
      <input type="hidden" v-model="form.editId" />
      <div class="row">
        <div>
          <label>Pavadinimas</label>
          <input v-model="form.pavadinimas" />
        </div>
        <div>
          <label>Miestas</label>
          <input v-model="form.miestas" />
        </div>
      </div>
      <div style="margin-top: 10px;">
        <label>Aprašymas</label>
        <textarea v-model="form.aprasymas" rows="3"></textarea>
      </div>
      <div class="row" style="margin-top: 10px;">
        <div>
          <label>Pradžios data (YYYY-MM-DD HH:MM:SS)</label>
          <input v-model="form.pradzios_data" placeholder="2025-12-30 18:00:00" />
        </div>
        <div>
          <label>Pabaigos data (nebūtina)</label>
          <input v-model="form.pabaigos_data" placeholder="2025-12-30 21:00:00" />
        </div>
      </div>
      <div style="margin-top: 10px;">
        <label>Adresas</label>
        <input v-model="form.adresas" />
      </div>
      <div class="row" style="margin-top: 12px;">
        <button class="btn" @click="save">Saugoti</button>
        <button class="btn" @click="clearForm">Išvalyti formą</button>
      </div>
    </div>
    <div class="card" v-if="registracijos.show" style="display: block;">
      <h3>Registracijos: {{ registracijos.pavad }}</h3>
      <p v-if="registracijos.loading">Kraunama registracijos...</p>
      <p v-else-if="!registracijos.list.length">Nėra registracijų.</p>
      <table v-else style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr>
            <th style="text-align: left; border-bottom: 1px solid #ddd; padding: 8px;">Vardas</th>
            <th style="text-align: left; border-bottom: 1px solid #ddd; padding: 8px;">El. paštas</th>
            <th style="text-align: left; border-bottom: 1px solid #ddd; padding: 8px;">Data</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="x in registracijos.list" :key="x.id">
            <td style="border-bottom: 1px solid #eee; padding: 8px;">{{ x.vartotojas?.vardas ?? '-' }}</td>
            <td style="border-bottom: 1px solid #eee; padding: 8px;">{{ x.vartotojas?.el_pastas ?? '-' }}</td>
            <td style="border-bottom: 1px solid #eee; padding: 8px;">{{ formatDate(x.sukurta) }}</td>
          </tr>
        </tbody>
      </table>
      <div style="margin-top: 12px;">
        <button class="btn" @click="registracijos.show = false">Uždaryti</button>
      </div>
    </div>
    <div class="card">
      <p v-if="loading">Kraunama...</p>
      <p v-else-if="!mine.length">Neturi sukurtų renginių.</p>
      <div v-else>
        <h3>Mano sukurti renginiai</h3>
        <div v-for="r in mine" :key="r.id" class="card">
          <b>{{ r.pavadinimas }}</b> — {{ r.miestas }} — {{ formatDate(r.pradzios_data) }}<br>
          <span class="muted">Pabaiga: {{ formatDate(r.pabaigos_data) || 'Nenurodyta' }} | ID: {{ r.id }}</span>
          <div class="row" style="margin-top: 10px;">
            <button class="btn" @click="edit(r)">Redaguoti</button>
            <button class="btn" @click="del(r.id)">Trinti</button>
            <button class="btn" @click="loadRegistracijos(r.id, r.pavadinimas)">Registracijos</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(true);
const mine = ref([]);
const form = ref({
  editId: '',
  pavadinimas: '',
  miestas: '',
  aprasymas: '',
  pradzios_data: '',
  pabaigos_data: '',
  adresas: '',
});
const registracijos = ref({ show: false, pavad: '', list: [], loading: false });

onMounted(async () => {
  const token = localStorage.getItem('token');
  if (!token) {
    router.push('/prisijungti');
    return;
  }
  const meRes = await fetch('/api/as', {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  });
  if (!meRes.ok) {
    router.push('/prisijungti');
    return;
  }
  const meData = await meRes.json();
  const myId = meData.vartotojas?.id ?? meData.id ?? null;

  const res = await fetch('/api/auto-renginiai', {
    headers: { Accept: 'application/json' },
  });
  if (res.ok) {
    const data = await res.json();
    const all = data.auto_renginiai || [];
    mine.value = myId ? all.filter(r => Number(r.organizatorius_id) === Number(myId)) : [];
  }
  loading.value = false;
});

function clearForm() {
  form.value = {
    editId: '',
    pavadinimas: '',
    miestas: '',
    aprasymas: '',
    pradzios_data: '',
    pabaigos_data: '',
    adresas: '',
  };
}

function edit(r) {
  form.value.editId = r.id;
  form.value.pavadinimas = r.pavadinimas ?? '';
  form.value.miestas = r.miestas ?? '';
  form.value.aprasymas = r.aprasymas ?? '';
  form.value.pradzios_data = (r.pradzios_data ?? '').replace('T', ' ').substring(0, 19);
  form.value.pabaigos_data = r.pabaigos_data ? String(r.pabaigos_data).replace('T', ' ').substring(0, 19) : '';
  form.value.adresas = r.adresas ?? '';
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function del(id) {
  if (!confirm('Ar tikrai trinti?')) return;
  const token = localStorage.getItem('token');
  const res = await fetch(`/api/auto-renginiai/${id}`, {
    method: 'DELETE',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  });
  const data = await res.json();
  if (!res.ok) {
    alert(`Klaida ${res.status}: ${data.zinute ?? data.message ?? 'Nepavyko'}`);
  } else {
    alert(data.zinute ?? 'Ištrinta');
    location.reload(); // paprastas perkrovimas
  }
}

async function save() {
  const token = localStorage.getItem('token');
  const data = { ...form.value };
  delete data.editId;
  if (!data.pavadinimas || !data.miestas || !data.pradzios_data) {
    alert('Reikia: pavadinimas, miestas, pradžios data');
    return;
  }
  const url = form.value.editId ? `/api/auto-renginiai/${form.value.editId}` : '/api/auto-renginiai';
  const method = form.value.editId ? 'PUT' : 'POST';
  const res = await fetch(url, {
    method,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(data),
  });
  const payload = await res.json();
  if (!res.ok) {
    alert(`Klaida ${res.status}: ${payload.zinute ?? payload.message ?? 'Nepavyko'}`);
  } else {
    alert(payload.zinute ?? 'OK');
    clearForm();
    location.reload();
  }
}

async function loadRegistracijos(id, pavad) {
  const token = localStorage.getItem('token');
  registracijos.value = { show: true, pavad, list: [], loading: true };
  const res = await fetch(`/api/auto-renginiai/${id}/registracijos`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  });
  if (res.ok) {
    const data = await res.json();
    registracijos.value.list = data.registracijos ?? [];
  } else {
    registracijos.value.list = [];
  }
  registracijos.value.loading = false;
}

function formatDate(value) {
  if (!value) return '';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  const pad = (n) => String(n).padStart(2, '0');
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
}
</script>

<style scoped>
.row {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.row > div {
  flex: 1;
  min-width: 240px;
}
.btn {
  padding: 8px 12px;
  border: 1px solid #333;
  border-radius: 8px;
  background: #fff;
  cursor: pointer;
  margin-right: 8px;
}
input, textarea {
  width: 100%;
  padding: 8px;
  margin-top: 6px;
  border: 1px solid #ccc;
  border-radius: 8px;
}
label {
  font-size: 14px;
}
.card {
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 12px;
  margin: 10px 0;
}
.muted {
  color: #666;
  font-size: 14px;
}
</style>
