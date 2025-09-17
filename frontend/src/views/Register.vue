<template>
  <div class="min-h-screen w-screen flex items-center justify-center bg-green-500">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
      <h1 class="text-2xl font-bold text-black mb-6 text-center">Registrar</h1>

      <form @submit.prevent="register" class="space-y-4">
        <div>
          <label class="block text-black font-semibold mb-1">Nome</label>
          <input v-model="name" type="text" required
            class="w-full text-black px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400" />
        </div>

        <div>
          <label class="block text-black font-semibold mb-1">Email</label>
          <input v-model="email" type="email" required
            class="w-full text-black px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400" />
        </div>

        <div>
          <label class="block text-black font-semibold mb-1">Senha</label>
          <input v-model="password" type="password" required
            class="w-full text-black px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400" />
        </div>

        <div>
          <label class="block text-black font-semibold mb-1">Confirmar Senha</label>
          <input v-model="password_confirmation" type="password" required
            class="w-full text-black px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400" />
        </div>

        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
          Registrar
        </button>
      </form>

      <p class="mt-4 text-center text-black">
        Já tem conta?
        <a href="/login" class="text-green-700 font-semibold hover:underline">Entrar</a>
      </p>
    </div>
  </div>
</template>

<script>
import api from "../api.js";

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    };
  },
  methods: {
    async register() {
      try {
        const res = await api.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
        });

        console.log("Resposta do backend:", res.data);

        const userName = res.data.user?.name || "Usuário";

        localStorage.setItem('token', res.data.access_token);
        alert(`Bem-vindo, ${userName}`);

        this.$router.push('/login');

      } catch (err) {
        console.error("Erro ao registrar:", err);
        alert(err.response?.data?.message || "Erro ao registrar usuário");
      }
    }
  }
}
</script>
