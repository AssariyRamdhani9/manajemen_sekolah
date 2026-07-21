<template>
  <AppLayout role="admin">
    <!-- Header Banner -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 rounded-3xl text-white shadow-xl border border-indigo-900/40 relative overflow-hidden">
      <!-- Decorator Blob -->
      <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
      
      <div class="relative z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold mb-3">
          <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
          Control Panel System
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Dashboard Administrator</h1>
        <p class="text-slate-300 text-sm mt-1">Selamat datang kembali! Ringkasan aktivitas dan operasional sekolah hari ini.</p>
      </div>

      <div class="relative z-10 flex items-center gap-3">
        <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10 text-xs text-slate-200 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V8H3v11a2 2 0 002 2z" />
          </svg>
          {{ currentDate }}
        </div>
        <button 
          @click="refreshData" 
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-2xl transition-all shadow-lg shadow-indigo-600/30 flex items-center gap-2 active:scale-95"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6" />
          </svg>
          <span>Refresh</span>
        </button>
      </div>
    </div>

    <!-- Stats Cards Grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <!-- Card: Jumlah Siswa -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">{{ stats.students.toLocaleString() }}</h3>
            <span class="inline-flex items-center gap-1 mt-2.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
              ↑ +12% bulan ini
            </span>
          </div>
          <div class="p-3.5 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21h3a2 2 0 002-2V7a2 2 0 00-2-2H9.5L9 2H5a2 2 0 00-2 2v14a2 2 0 002 2h4.5l.5 2h4l.5-2H17z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Card: Jumlah Guru -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tenaga Pengajar</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">{{ stats.teachers.toLocaleString() }}</h3>
            <span class="inline-flex items-center gap-1 mt-2.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
              +3 guru baru
            </span>
          </div>
          <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2M12 12a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Card: Kelas Aktif -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kelas Aktif</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">{{ stats.activeClasses.toLocaleString() }}</h3>
            <span class="inline-flex items-center gap-1 mt-2.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
              100% Berjalan
            </span>
          </div>
          <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 4h4" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Card: Tugas Terkirim -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengumpulan Tugas</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">{{ stats.submittedAssignments }}%</h3>
            <div class="w-32 bg-slate-100 rounded-full h-2 mt-3 overflow-hidden">
              <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-500" :style="{ width: stats.submittedAssignments + '%' }"></div>
            </div>
          </div>
          <div class="p-3.5 rounded-2xl bg-purple-50 text-purple-600 border border-purple-100 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </section>

    <!-- Chart Section -->
    <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 mb-8">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h2 class="text-lg font-bold text-slate-900">Pertumbuhan Siswa & Kehadiran</h2>
          <p class="text-xs text-slate-500 mt-0.5">Statistik pendaftaran dan aktivitas pembelajaran tahun ajaran berjalan.</p>
        </div>
        <button @click="refreshChartData" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 w-fit">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6" />
          </svg>
          Update Grafik
        </button>
      </div>

      <div v-if="chartLoaded" class="relative h-72 sm:h-80">
        <Line :data="chartData" :options="chartOptions" :key="chartKey" />
      </div>
      <div v-else class="flex items-center justify-center h-80 text-slate-400">
        <div class="text-center">
          <div class="w-8 h-8 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
          <p class="text-xs">Memuat data statistik...</p>
        </div>
      </div>
    </section>

    <!-- Quick Management Shortcuts -->
    <section class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <Link href="/admin/students" class="p-5 bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-1 transition-all duration-300 group">
        <div class="flex items-center justify-between">
          <span class="text-sm font-bold">Kelola Siswa</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </div>
        <p class="text-xs text-indigo-100 mt-2">Tambah, ubah, atau hapus data siswa.</p>
      </Link>

      <Link href="/admin/teachers" class="p-5 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40 hover:-translate-y-1 transition-all duration-300 group">
        <div class="flex items-center justify-between">
          <span class="text-sm font-bold">Kelola Guru</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </div>
        <p class="text-xs text-purple-100 mt-2">Pengaturan NIP & penugasan staf pengajar.</p>
      </Link>

      <Link href="/admin/classes" class="p-5 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-2xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all duration-300 group">
        <div class="flex items-center justify-between">
          <span class="text-sm font-bold">Kelola Kelas</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </div>
        <p class="text-xs text-emerald-100 mt-2">Struktur kelas X, XI, XII & wali kelas.</p>
      </Link>

      <Link href="/admin/subjects" class="p-5 bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-2xl shadow-lg shadow-amber-500/20 hover:shadow-amber-500/40 hover:-translate-y-1 transition-all duration-300 group">
        <div class="flex items-center justify-between">
          <span class="text-sm font-bold">Mata Pelajaran</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </div>
        <p class="text-xs text-amber-100 mt-2">Daftar kurikulum & kode mata pelajaran.</p>
      </Link>
    </section>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler,
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
)

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric',
})

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      students: 0,
      teachers: 0,
      activeClasses: 0,
      submittedAssignments: 0,
    }),
  },
})

const stats = reactive({
  students: props.stats.students,
  teachers: props.stats.teachers,
  activeClasses: props.stats.activeClasses,
  submittedAssignments: props.stats.submittedAssignments,
})

const chartLoaded = ref(false)
const chartKey = ref(0)

const chartData = reactive({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
  datasets: [
    {
      label: 'Jumlah Siswa Aktif',
      backgroundColor: 'rgba(99, 102, 241, 0.15)',
      borderColor: 'rgba(99, 102, 241, 1)',
      borderWidth: 3,
      pointBackgroundColor: 'rgba(99, 102, 241, 1)',
      pointBorderColor: '#ffffff',
      pointBorderWidth: 2,
      pointRadius: 4,
      data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
      fill: true,
      tension: 0.4,
    },
  ],
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
      labels: { font: { family: 'Plus Jakarta Sans', size: 12 } }
    },
  },
  scales: {
    y: { grid: { color: 'rgba(226, 232, 240, 0.6)' } },
    x: { grid: { display: false } },
  },
}

onMounted(() => {
  nextTick(() => {
    chartLoaded.value = true
  })
})

const refreshData = () => {
  stats.students += Math.floor(Math.random() * 10)
  stats.teachers += Math.floor(Math.random() * 2)
  stats.activeClasses += Math.floor(Math.random() * 2)
}

const refreshChartData = () => {
  chartData.datasets[0].data = chartData.datasets[0].data.map((val) => 
    val + Math.floor(Math.random() * 60 - 30)
  )
  chartKey.value++
}
</script>