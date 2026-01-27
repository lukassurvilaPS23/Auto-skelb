<template>
  <div>
    <h1>Auto renginiai</h1>
    <div class="card hero">
      <div class="row">
        <div class="hero-left">
          <p class="muted">Platforma entuziastams</p>
          <h2>Atrask, organizuok ir valdyk auto renginius.</h2>
          <div class="hero-buttons">
            <a href="/renginiai" class="btn primary">Peržiūrėti renginius</a>
            <a href="/mano-renginiai" class="btn">Mano renginiai</a>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="card">
        <h3>Greitos nuorodos</h3>
        <ul>
          <li><a href="/renginiai">Renginių sąrašas</a></li>
          <li><a href="/prisijungti">Prisijungti</a></li>
          <li><a href="/registruotis">Registruotis</a></li>
          <li><a href="/xml" target="_blank">XML eksportas</a></li>
          <li><a href="/swagger" target="_blank">Swagger dokumentacija</a></li>
        </ul>
      </div>
      <div class="card">
        <h3>Naujausi renginiai</h3>
        <p v-if="loading">Kraunama...</p>
        <p v-else-if="!events.length">Šiuo metu nėra aktyvių renginių.</p>
        <ul v-else>
          <li v-for="e in events" :key="e.id">
            <a :href="`/renginiai/${e.id}`">{{ e.pavadinimas }}</a>
            <span class="muted">{{ e.miestas }} · {{ formatDate(e.pradzios_data) }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const events = ref([]);
const loading = ref(true);

onMounted(async () => {
  const res = await fetch('/api/auto-renginiai', {
    headers: { Accept: 'application/json' },
  });
  if (res.ok) {
    const data = await res.json();
    events.value = (data.auto_renginiai || []).slice(0, 4);
  }
  loading.value = false;
});

function formatDate(value) {
  if (!value) return '';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  const pad = (n) => String(n).padStart(2, '0');
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
}
</script>

<style scoped>
.hero {
  background: linear-gradient(135deg, #eff6ff, #eef2ff);
  border: none;
  box-shadow: 0 12px 25px rgba(15, 23, 42, 0.12);
}
.hero-left {
  flex: 1;
  min-width: 240px;
}
.hero-right {
  flex: 1;
  min-width: 220px;
}
.hero-left h2 {
  margin: 6px 0 12px;
  font-size: 30px;
}
.hero-left p {
  max-width: 420px;
  color: #374151;
}
.hero-buttons {
  margin-top: 16px;
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.btn.primary {
  background: #111827;
  color: #fff;
  border-color: #111827;
}
.inner {
  background: #fff;
  border-radius: 14px;
  padding: 20px;
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}
.inner ul {
  margin: 12px 0 0;
  padding-left: 18px;
  line-height: 1.8;
  color: #1f2937;
}
.row {
  display: flex;
  gap: 18px;
  margin-top: 18px;
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
