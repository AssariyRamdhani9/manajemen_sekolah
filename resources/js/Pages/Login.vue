<template>
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 px-4">
    <div
      class="w-full max-w-md p-8 bg-white rounded-2xl shadow-2xl border border-gray-100 transition-transform duration-300 hover:scale-[1.02]"
    >
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <img
          src="https://www.svgrepo.com/show/496032/book-closed.svg"
          alt="Logo"
          class="h-14 w-14"
        />
      </div>

      <!-- Judul -->
      <h2 class="text-3xl font-extrabold text-center text-gray-800 tracking-wide">
        Selamat Datang!
      </h2>
      <p class="text-center text-gray-500 mt-2">
        Silakan masuk ke akun Anda untuk melanjutkan.
      </p>

      <!-- Form Login -->
      <form @submit.prevent="handleLogin" class="mt-6 space-y-5">
        <div>
          <label class="block text-gray-700 font-medium">Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="Masukkan email"
              class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition duration-200"
              required
          />
        </div>

        <div>
          <label class="block text-gray-700 font-medium">Password</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Masukkan password"
            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition duration-200"
            required
          />
        </div>

        <!-- Pilih Role -->
        <div>
          <label class="block text-gray-700 font-medium">Login Sebagai</label>
          <select
            v-model="form.role"
            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition duration-200"
          >
            <option disabled value="">Pilih Role</option>
            <option value="admin">Admin</option>
            <option value="guru">Guru</option>
            <option value="siswa">Siswa</option>
          </select>
        </div>

        <p v-if="errorMessage" class="text-red-500 text-sm text-center">
          {{ errorMessage }}
        </p>

        <button
          type="submit"
          class="w-full py-3 mt-3 text-white font-semibold rounded-lg shadow-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition duration-300"
        >
          Masuk
        </button>
      </form>

      <!-- Link Register -->
      <p class="mt-6 text-center text-gray-600 text-sm">
        Belum punya akun?
        <a href="/register" class="text-indigo-600 hover:underline font-medium">
          Daftar sekarang
        </a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import axios from "axios";


const form = reactive({
  email: "",
  password: "",
  role: "",
});

const errorMessage = ref("");

const handleLogin = async () => {
  try {
    const res = await axios.post("http://127.0.0.1:8000/api/login", {
      email: form.email,
      password: form.password,
       role: form.role, 
    });

    // Simpan token & user
    localStorage.setItem("token", res.data.token);
    localStorage.setItem("user", JSON.stringify(res.data.user));

    // Redirect sesuai role
    if (res.data.user.role === "admin") {
      window.location.href = "/dashboard/admin";
    } else if (res.data.user.role === "guru") {
      window.location.href = "/dashboard/teacher";
    } else {
      window.location.href = "/dashboard/siswa";
    }

  } catch (err) {
    errorMessage.value = "Email atau password salah!";
  }
};

</script>
