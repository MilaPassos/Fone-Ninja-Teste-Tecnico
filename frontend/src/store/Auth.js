// src/store/auth.js
import { reactive } from "vue";

export const auth = reactive({
  user: JSON.parse(localStorage.getItem("user")) || null,
  token: localStorage.getItem("token") || null,

  login(user, token) {
    this.user = user;
    this.token = token;
    localStorage.setItem("user", JSON.stringify(user));
    localStorage.setItem("token", token);
  },

  logout() {
    this.user = null;
    this.token = null;
    localStorage.removeItem("user");
    localStorage.removeItem("token");
  },

  isLoggedIn() {
    return !!this.user;
  },
});
