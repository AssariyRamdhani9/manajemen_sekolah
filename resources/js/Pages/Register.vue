<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4 relative overflow-hidden font-sans">
    <!-- Ambient Background Lighting Gradients -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
      <!-- Card Container -->
      <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 rounded-3xl p-8 shadow-2xl shadow-indigo-950/50">
        
        <!-- Header & Logo -->
        <div class="text-center mb-6">
          <div class="inline-flex p-3.5 rounded-2xl bg-gradient-to-tr from-blue-500 via-teal-500 to-emerald-500 text-white shadow-xl shadow-teal-500/25 mb-4 ring-4 ring-slate-900/50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
          </div>
          <h1 class="text-2xl font-extrabold text-white tracking-tight">Daftar Akun Baru</h1>
          <p class="text-xs text-slate-400 mt-1.5">Buat akun untuk mengakses portal sekolah</p>
        </div>

        <form @submit.prevent="submitRegister" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Lengkap</label>
            <input
              v-model="form.full_name"
              type="text"
              placeholder="Masukkan nama lengkap"
              class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="nama@sekolah.sch.id"
              class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password</label>
            <input
              v-model="form.password"
              type="password"
              placeholder="Minimal 6 karakter"
              class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Konfirmasi Password</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              placeholder="Ketik ulang password"
              class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
              required
            />
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="w-full py-3.5 px-4 mt-2 bg-gradient-to-r from-teal-500 via-emerald-500 to-blue-600 hover:from-teal-600 hover:to-blue-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-500/25 transition-all duration-300 transform active:scale-[0.98] disabled:opacity-60 flex items-center justify-center gap-2"
          >
            <span v-if="isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span>{{ isLoading ? 'Mendaftar...' : 'Daftar Akun' }}</span>
          </button>
        </form>

        <p class="mt-6 text-center text-xs text-slate-400">
          Sudah memiliki akun?
          <a href="/login" class="text-teal-400 hover:text-teal-300 font-semibold hover:underline transition-colors ml-1">
            Masuk di sini
          </a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";

const form = reactive({
  full_name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const isLoading = ref(false);

const submitRegister = () => {
  isLoading.value = true;
  router.post("/api/register", form, {
    onSuccess: () => {
      alert("Pendaftaran berhasil! Silakan login.");
      router.visit("/login");
    },
    onError: () => {
      alert("Pendaftaran gagal. Periksa kembali data Anda.");
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};
</script>
