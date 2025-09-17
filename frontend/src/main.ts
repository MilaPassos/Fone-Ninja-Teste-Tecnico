import { createApp } from 'vue';
import App from './App.vue';
import { createRouter, createWebHistory } from 'vue-router';
import Login from './views/Login.vue';
import Register from './views/Register.vue';
import './style.css';
import Produtos from './views/Produtos.vue';
import Histórico from './views/Historico.vue';

const routes = [
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { 
    path: '/produtos', 
    component: Produtos,
    meta: { requiresAuth: true } 
  },  
  { 
    path: '/historico', 
    component: Histórico,
    meta: { requiresAuth: true } 
  },  
  { path: '/', redirect: '/login' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const user = JSON.parse(localStorage.getItem('user') || '{}');

  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else if (to.meta.role && user.role !== to.meta.role) {
    alert('Você não tem permissão para acessar esta página.');
    next('/login');
  } else {
    next();
  }
});

createApp(App).use(router).mount('#app');
