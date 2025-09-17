<template>
  <header class="bg-white text-black p-4 flex justify-between items-center shadow">
    <h1 class="font-bold text-lg">ERP Estoque</h1>

    <nav class="flex items-center gap-4">
      <router-link v-if="auth.isLoggedIn()" to="/produtos" class="hover:underline">
        Meus Produtos
      </router-link>
      <router-link v-if="auth.isLoggedIn()" to="/historico" class="hover:underline">
        Histórico
      </router-link>
    </nav>

    <div class="flex items-center gap-4">
      <span v-if="auth.user">Bem-vindo, {{ auth.user.name }}</span>
      <button v-if="auth.isLoggedIn()" @click="logout" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
        Sair
      </button>
    </div>
  </header>
</template>

<script>
import { auth } from "../store/auth.js";

export default {
  setup() {
    return { auth };
  },
  methods: {
    logout() {
      auth.logout();
      this.$router.push('/login');
    }
  }
}
</script>
