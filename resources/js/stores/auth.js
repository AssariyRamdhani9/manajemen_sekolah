// src/stores/auth.js

import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.user && state.user.role === 'admin',
    isTeacher: (state) => state.user && state.user.role === 'teacher',
    isStudent: (state) => state.user && state.user.role === 'student',
  },

  actions: {
    async login(email, password) {
      const response = await axios.post('http://127.0.0.1:8000/api/auth/login', { email, password });
      
      this.token = response.data.access_token;
      this.user = response.data.user;

      // Simpan di localStorage agar user tetap login setelah refresh
      localStorage.setItem('token', this.token);
      localStorage.setItem('user', JSON.stringify(this.user));

      // Set default header untuk Axios
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    },

    async register(userData) {
      await axios.post('http://127.0.0.1:8000/api/auth/register', userData);
    },

    async logout() {
      await axios.post('http://127.0.0.1:8000/api/auth/logout');
      
      // Hapus data dari state dan localStorage
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    },

    initialize() {
      // Ambil token dari localStorage saat aplikasi dimuat
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      }
    },
  },
});