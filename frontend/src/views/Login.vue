<template>
  <div class="min-h-screen w-screen flex items-center justify-center bg-green-500">
    <div class="bg-white border border-black p-8 rounded-lg shadow-lg w-96">
      <h1 class="text-2xl font-bold text-black mb-6 text-center">Login</h1>

      <form @submit.prevent="login" class="space-y-4">
        <div>
          <label class="block text-black font-semibold mb-1">Email</label>
          <input v-model="email" type="email" required
                 class="w-full px-4 py-2 border border-black rounded focus:outline-none focus:ring-2 focus:ring-green-400"/>
        </div>

        <div>
          <label class="block text-black font-semibold mb-1">Senha</label>
          <input v-model="password" type="password" required
                 class="w-full px-4 py-2 border border-black rounded focus:outline-none focus:ring-2 focus:ring-green-400"/>
        </div>

        <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
          Entrar
        </button>
      </form>

      <p class="mt-4 text-center text-black">
        Não tem conta?
        <a href="/register" class="text-green-700 font-semibold hover:underline">Registrar</a>
      </p>
    </div>
  </div>
</template>

<script>
import api from "../api.js";
import { useRouter } from "vue-router";
import { auth } from "../store/auth.js";

export default {
  data() {
    return {
      email: '',
      password: '',
    };
  },
  setup() {
    const router = useRouter();
    return { router };
  },
  methods: {
    async login() {
      try {
        const res = await api.post('/login', {
          email: this.email,
          password: this.password
        });

        auth.login(res.data.user, res.data.access_token);

        this.router.push('/produtos');
      } catch (err) {
        console.log(err.response?.data?.message || "Erro ao fazer login");
      }
    }
  }
}

</script>
