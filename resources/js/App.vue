<template>
  <div>
    <nav>
      <a href="/">Pagrindinis</a>
      <a href="/renginiai">Auto renginiai</a>
      <a href="/mano-renginiai">Mano renginiai</a>
      <a href="/xml" target="_blank">XML</a>
      <a href="/swagger" target="_blank">Swagger</a>
      <a href="/profilis">Profilis</a>
      <div class="right">
        <template v-if="isLoggedIn">
          <span>{{ user?.name }}</span>
          <button @click="logout">Atsijungti</button>
        </template>
        <template v-else>
          <a href="/prisijungti">Prisijungti</a>
          <a href="/registruotis">Registruotis</a>
        </template>
      </div>
    </nav>
    <div class="container">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isLoggedIn = ref(false);
const user = ref(null);

onMounted(() => {
  const token = localStorage.getItem('token');
  if (token) {
    isLoggedIn.value = true;
    fetchUser();
  }
});

async function fetchUser() {
  const token = localStorage.getItem('token');
  const res = await fetch('/api/as', {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  });
  if (res.ok) {
    const data = await res.json();
    user.value = data.vartotojas;
  } else {
    localStorage.removeItem('token');
    isLoggedIn.value = false;
  }
}

async function logout() {
  const token = localStorage.getItem('token');
  await fetch('/api/atsijungti', {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  });
  localStorage.removeItem('token');
  isLoggedIn.value = false;
  user.value = null;
  router.push('/');
}
</script>

<style scoped>
nav {
  padding: 12px;
  border-bottom: 1px solid #ddd;
  display: flex;
  gap: 12px;
  align-items: center;
}
nav a {
  text-decoration: none;
  color: #111;
}
nav a:hover {
  text-decoration: underline;
}
.right {
  margin-left: auto;
  display: flex;
  gap: 12px;
  align-items: center;
}
.container {
  max-width: 1000px;
  margin: 18px auto;
  padding: 0 12px;
}
</style>
