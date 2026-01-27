<template>
  <div>
    <h1>Profilis</h1>
    <div class="card">
      <h3>Vartotojo informacija</h3>
      <p id="profileMessage" class="muted">{{ message }}</p>
      <div v-if="user">
        <div class="row" style="gap: 24px; align-items: flex-start;">
          <div style="flex: 1; min-width: 220px;">
            <p><strong>Vardas</strong><br>{{ user.name }}</p>
            <p><strong>El. paštas</strong><br>{{ user.email }}</p>
            <p><strong>Rolės</strong><br>{{ rolesDisplay }}</p>
          </div>
          <div style="flex: 1; min-width: 220px;">
            <p><strong>Prisijungė</strong><br>{{ formatDate(user.created_at) }}</p>
            <p><strong>ID</strong><br>{{ user.id }}</p>
          </div>
        </div>
      </div>
      <details style="margin-top: 16px;">
        <summary>Rodyti JSON</summary>
        <pre style="margin-top: 12px;">{{ JSON.stringify(user, null, 2) }}</pre>
      </details>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);
const message = ref('Kraunama...');

const rolesDisplay = computed(() => {
  if (!user.value) return '—';
  let roles = 'Nenurodyta';
  if (Array.isArray(user.value.roles) && user.value.roles.length) {
    roles = user.value.roles.map(r => r.name ?? r).join(', ');
  } else if (typeof user.value.roles === 'string' && user.value.roles.trim()) {
    roles = user.value.roles;
  } else if (Array.isArray(user.value.all_roles) && user.value.all_roles.length) {
    roles = user.value.all_roles.map(r => r.name ?? r).join(', ');
  }
  return roles || '—';
});

onMounted(async () => {
  const token = localStorage.getItem('token');
  if (!token) {
    message.value = 'Neprisijungęs.';
    router.push('/prisijungti');
    return;
  }
  const res = await fetch('/api/as', {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  });
  if (!res.ok) {
    message.value = `Neprisijungęs (status ${res.status}).`;
    localStorage.removeItem('token');
    router.push('/prisijungti');
    return;
  }
  const data = await res.json();
  user.value = data.vartotojas ?? data ?? {};
  message.value = 'Prisijungęs.';
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
.row {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
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
