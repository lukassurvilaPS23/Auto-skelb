<template>
  <div>
    <h1>Auto renginys</h1>
    <div class="card" v-if="loading">Kraunama...</div>
    <div class="card" v-else-if="!event">Renginys nerastas.</div>
    <div v-else>
      <div class="card">
        <h2>{{ event.pavadinimas }}</h2>
        <p>{{ event.aprasymas }}</p>
        <p><b>Miestas:</b> {{ event.miestas }}</p>
        <p><b>Adresas:</b> {{ event.adresas }}</p>
        <p><b>Pradžia:</b> {{ formatDate(event.pradzios_data) }}</p>
        <p><b>Pabaiga:</b> {{ formatDate(event.pabaigos_data) }}</p>
        <p><b>Statusas:</b> {{ event.statusas }}</p>
      </div>
      <div class="card">
        <button class="btn" @click="register">Registruotis</button>
        <button class="btn" @click="unregister">Atšaukti registraciją</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const event = ref(null);
const loading = ref(true);

onMounted(async () => {
  const id = route.params.id;
  const res = await fetch(`/api/auto-renginiai/${id}`, {
    headers: { Accept: 'application/json' },
  });
  if (res.ok) {
    const data = await res.json();
    event.value = data.auto_renginys;
  }
  loading.value = false;
});

async function register() {
  const token = localStorage.getItem('token');
  if (!token) {
    alert('Prisijunk, kad galėtum registruotis.');
    router.push('/prisijungti');
    return;
  }
  const res = await fetch(`/api/auto-renginiai/${route.params.id}/registracija`, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  });
  const data = await res.json();
  if (!res.ok) {
    alert(`Klaida ${res.status}: ${data.zinute ?? data.message ?? 'Nepavyko'}`);
  } else {
    alert(data.zinute ?? 'Registracija sėkminga');
  }
}

async function unregister() {
  const token = localStorage.getItem('token');
  if (!token) {
    alert('Prisijunk, kad galėtum atšaukti registraciją.');
    router.push('/prisijungti');
    return;
  }
  const res = await fetch(`/api/auto-renginiai/${route.params.id}/registracija`, {
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
    alert(data.zinute ?? 'Registracija atšaukta');
  }
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
.btn {
  padding: 8px 12px;
  border: 1px solid #333;
  border-radius: 8px;
  background: #fff;
  cursor: pointer;
  margin-right: 8px;
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
