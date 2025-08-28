<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
      <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label for="email" class="block text-gray-700">Email</label>
          <input type="email" id="email" v-model="email" class="w-full mt-1 p-2 border border-gray-300 rounded-md" />
        </div>
        <div class="mb-6">
          <label for="password" class="block text-gray-700">Password</label>
          <input type="password" id="password" v-model="password" class="w-full mt-1 p-2 border border-gray-300 rounded-md" />
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">Login</button>
        <div v-if="error" class="text-red-500 mt-4 text-center">{{ error }}</div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const email = ref('');
const password = ref('');
const error = ref('');
const router = useRouter();
const authStore = useAuthStore();

const handleLogin = async () => {
  try {
    error.value = '';
    await authStore.login(email.value, password.value);
    router.push('/dashboard');
  } catch (err) {
    error.value = 'Login failed. Please check your credentials.';
    console.error(err);
  }
};
</script>