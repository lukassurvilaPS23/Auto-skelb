<template>
  <div>
    <h1>Auto renginiai</h1>
    <div class="card">
      <div class="row">
        <div>
          <label>Miestas</label>
          <input v-model="filters.miestas" placeholder="Pvz. Vilnius" />
        </div>
        <div>
          <button class="btn" @click="load">Filtruoti</button>
        </div>
      </div>
    </div>
    <div class="card" style="min-height: 120px;">
      <p v-if="loading">Kraunama...</p>
      <p v-else-if="!list.length">Renginių nėra.</p>
      <div v-else>
        <div v-for="r in list" :key="r.id" class="event-card">
          <div class="event-header">
            <div>
              <a :href="`/renginiai/${r.id}`" class="event-title">{{ r.pavadinimas }}</a>
              <p class="muted">{{ r.miestas }} · {{ formatDate(r.pradzios_data) }}</p>
            </div>
          </div>
          <p>{{ r.aprasymas }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const filters = ref({ miestas: '' });
const list = ref([]);
const loading = ref(true);

onMounted(load);

async function load() {
  loading.value = true;
  const qs = new URLSearchParams();
  if (filters.value.miestas) qs.set('miestas', filters.value.miestas);
    const url = '/api/auto-renginiai' + (qs.toString() ? `?${qs}` : '');
  const res = await fetch(url, { headers: { Accept: 'application/json' } });
  if (res.ok) {
    const data = await res.json();
    list.value = data.auto_renginiai || [];
  }
  loading.value = false;
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
}
input {
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
.event-card {
  padding: 14px 0;
  border-bottom: 1px solid #eee;
}
.event-card:last-child {
  border-bottom: none;
}
.event-header {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.event-title {
  font-size: 18px;
  font-weight: 600;
  color: #111827;
  text-decoration: none;
}
.muted {
  color: #666;
  font-size: 14px;
  margin: 4px 0 0;
}
.status-badge {
  align-self: flex-start;
  background: #eef2ff;
  color: #312e81;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 13px;
}
</style>
