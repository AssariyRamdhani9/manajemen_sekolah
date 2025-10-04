<template>
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-500 via-green-400 to-teal-500 px-4">
    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-2xl border border-blue-100 transition-transform duration-300 hover:scale-[1.02]">
      
      <div class="flex justify-center mb-6">
        <img
          src="https://www.svgrepo.com/show/496032/book-closed.svg"
          alt="Logo Sekolah"
          class="h-14 w-14 text-blue-600 animate-bounce"
        />
      </div>

      <h2 class="text-3xl font-extrabold text-center text-gray-800 tracking-wide">
        Daftar Akun Baru
      </h2>
      <p class="text-center text-gray-500 mt-2">Akses portal sekolah dengan mudah!</p>

      <form @submit.prevent="submitRegister" class="mt-6 space-y-5">
        <div>
          <label for="name" class="block text-gray-700 font-medium">Nama Lengkap</label>
          <input
            v-model="form.full_name"
            type="text"
            id="name"
            placeholder="Masukkan nama lengkap Anda"
            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition duration-200"
            required
          />
        </div>

        <div>
          <label for="email" class="block text-gray-700 font-medium">Email</label>
          <input
            v-model="form.email"
            type="email"
            id="email"
            placeholder="contoh@sekolah.ac.id"
            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition duration-200"
            required
          />
        </div>

        <div>
          <label for="password" class="block text-gray-700 font-medium">Password</label>
          <input
            v-model="form.password"
            type="password"
            id="password"
            placeholder="Minimal 6 karakter"
            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition duration-200"
            required
          />
        </div>

        <div>
          <label for="password_confirmation" class="block text-gray-700 font-medium">Konfirmasi Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            id="password_confirmation"
            placeholder="Ketik ulang password Anda"
            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition duration-200"
            required
          />
        </div>

        <button
          type="submit"
          class="w-full py-3 mt-3 text-white font-semibold rounded-lg shadow-lg bg-gradient-to-r from-blue-600 to-teal-600 hover:from-blue-700 hover:to-teal-700 transition duration-300"
        >
          Daftar
        </button>
      </form>

      <p class="mt-6 text-center text-gray-600 text-sm">
        Sudah punya akun?
        <a href="/login" class="text-blue-600 hover:underline font-medium">Masuk di sini</a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from "vue";
import { router } from "@inertiajs/vue3";

const form = reactive({
  full_name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const submitRegister = () => {
  router.post("/register", form, {
    onSuccess: () => {
      alert("Pendaftaran berhasil! Silakan login.");
      router.visit("/login");
    },
    onError: () => {
      alert("Pendaftaran gagal. Periksa kembali data Anda.");
    },
  });
};
</script>
