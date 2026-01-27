<template>
  <div>
    <h1>Prisijungti</h1>
    <div class="card">
      <form @submit.prevent="submit">
        <div>
          <label>El. paštas</label>
          <input v-model="form.el_pastas" type="email" required />
        </div>
        <div style="margin-top: 10px;">
          <label>Slaptažodis</label>
          <input v-model="form.slaptazodis" type="password" required />
        </div>
        <div style="margin-top: 12px;">
          <button class="btn" type="submit">Prisijungti</button>
        </div>
      </form>
      <p v-if="error" style="color: red; margin-top: 12px;">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = ref({ el_pastas: '', slaptazodis: '' });
const error = ref('');

async function submit() {
  error.value = '';
  const res = await fetch('/api/prisijungti', {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(form.value),
  });
  const data = await res.json();
  if (!res.ok) {
    error.value = data.message || 'Klaida';
    return;
  }
  localStorage.setItem('token', data.token);
  router.push('/');
}
</script>

<style scoped>
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
</style>
