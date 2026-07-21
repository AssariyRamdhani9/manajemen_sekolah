<template>
  <AppLayout role="guru">
    <!-- Header Banner Guru -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-900 p-6 sm:p-8 rounded-3xl text-white shadow-xl border border-emerald-900/40 relative overflow-hidden">
      <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

      <div class="relative z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-semibold mb-3">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          Portal Pengajar & Akademik
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Dashboard Guru</h1>
        <p class="text-emerald-100 text-sm mt-1">Kelola jadwal mengajar, materi pembelajaran, dan penilaian siswa hari ini.</p>
      </div>

      <div class="relative z-10 flex items-center gap-3">
        <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10 text-xs text-slate-200 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V8H3v11a2 2 0 002 2z" />
          </svg>
          {{ todayFull }}
        </div>
        <button 
          @click="refreshAll" 
          class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-2xl transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2 active:scale-95"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6" />
          </svg>
          <span>Refresh</span>
        </button>
      </div>
    </div>

    <!-- Summary Grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kelas Diampu</p>
        <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">{{ summary.classes }}</h3>
        <p class="text-xs text-slate-500 mt-2">Kelas aktif semester ini</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa</p>
        <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">{{ summary.students }}</h3>
        <p class="text-xs text-slate-500 mt-2">Total siswa didik</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tugas Perlu Dinilai</p>
        <h3 class="text-3xl font-extrabold text-amber-600 mt-2 tracking-tight">{{ summary.ungraded }}</h3>
        <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
          Perlu koreksi
        </span>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengumuman Baru</p>
        <h3 class="text-3xl font-extrabold text-emerald-600 mt-2 tracking-tight">{{ announcements.length }}</h3>
        <p class="text-xs text-slate-500 mt-2">Pengumuman sekolah</p>
      </div>
    </section>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Schedule List -->
      <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-lg font-bold text-slate-900">Jadwal Mengajar Hari Ini</h2>
            <p class="text-xs text-slate-500">Daftar kelas yang harus Anda ajar hari ini</p>
          </div>
          <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">{{ todayShort }}</span>
        </div>

        <div v-if="todaySchedule.length" class="space-y-3">
          <div 
            v-for="item in todaySchedule" 
            :key="item.id" 
            class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:bg-white hover:border-emerald-200 hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
          >
            <div class="flex items-start gap-3">
              <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600 font-bold text-xs shrink-0 border border-emerald-100">
                {{ item.time }}
              </div>
              <div>
                <h4 class="font-bold text-slate-800 text-base leading-tight">{{ item.subject }}</h4>
                <p class="text-xs text-slate-500 mt-1">Kelas: <span class="font-semibold text-slate-700">{{ item.className }}</span> • Ruang: <span class="font-semibold text-slate-700">{{ item.room }}</span></p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button @click="goToAttendance(item)" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                Absensi
              </button>
              <button @click="createAssignment(item)" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                Tugas
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12 text-slate-400 text-sm">
          Tidak ada jadwal mengajar untuk hari ini.
        </div>
      </div>

      <!-- Quick Actions & Announcements -->
      <div class="space-y-6">
        <!-- Quick Actions Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
          <h3 class="text-base font-bold text-slate-900 mb-4">Akses Cepat Guru</h3>
          <div class="space-y-2.5">
            <Link href="/teacher/assignments" class="w-full px-4 py-3 rounded-2xl bg-emerald-50 hover:bg-emerald-100/80 text-emerald-800 text-xs font-bold flex items-center justify-between transition-colors border border-emerald-200/60">
              <span>Buat Tugas Baru</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </Link>
            <Link href="/teacher/materials" class="w-full px-4 py-3 rounded-2xl bg-indigo-50 hover:bg-indigo-100/80 text-indigo-800 text-xs font-bold flex items-center justify-between transition-colors border border-indigo-200/60">
              <span>Unggah Materi Belajar</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
            </Link>
            <Link href="/teacher/announcements" class="w-full px-4 py-3 rounded-2xl bg-purple-50 hover:bg-purple-100/80 text-purple-800 text-xs font-bold flex items-center justify-between transition-colors border border-purple-200/60">
              <span>Buat Pengumuman</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
              </svg>
            </Link>
          </div>
        </div>

        <!-- Announcements Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
          <h3 class="text-base font-bold text-slate-900 mb-4">Pengumuman Baru</h3>
          <div class="space-y-3">
            <div v-for="ann in announcements" :key="ann.id" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
              <div class="flex justify-between items-start">
                <h5 class="font-bold text-slate-800 text-xs">{{ ann.title }}</h5>
                <span class="text-[10px] text-slate-400">{{ ann.time }}</span>
              </div>
              <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ ann.excerpt }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const summary = reactive({
  classes: 0,
  students: 0,
  ungraded: 0,
})

const announcements = ref([])

const todaySchedule = ref([])

const today = new Date()
const todayFull = today.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
const todayShort = today.toLocaleDateString('id-ID')

import { router } from '@inertiajs/vue3'

const refreshAll = () => {
  summary.students += Math.floor(Math.random() * 2)
}

const goToAttendance = (item) => {
  router.visit('/teacher/attendances')
}

const createAssignment = (item) => {
  router.visit('/teacher/assignments')
}
</script>
