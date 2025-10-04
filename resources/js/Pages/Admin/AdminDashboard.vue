<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar Component -->
    <Sidebar role="admin" />

    <!-- Main Content -->
    <main class="flex-grow p-8 sm:p-12 transition-all duration-300">
      <!-- Header -->
      <header class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-4xl font-extrabold text-gray-900 leading-tight">Dashboard Admin</h1>
            <p class="text-lg text-gray-600 mt-2">Selamat datang kembali! Ini adalah ringkasan kegiatan sekolah.</p>
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ currentDate }}
            </div>
            <button @click="refreshData" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Refresh Data
            </button>
          </div>
        </div>
      </header>

      <!-- Stats Cards -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card: Jumlah Siswa -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-blue-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 font-medium text-sm uppercase tracking-wide">Jumlah Siswa</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.students.toLocaleString() }}</p>
              <p class="text-sm text-green-600 mt-1 font-medium">+12% dari bulan lalu</p>
            </div>
            <div class="text-blue-500 p-3 bg-blue-100 rounded-full animate-pulse">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21h3a2 2 0 002-2V7a2 2 0 00-2-2H9.5L9 2H5a2 2 0 00-2 2v14a2 2 0 002 2h4.5l.5 2h4l.5-2H17zM12 14v4M10 16h4"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Card: Jumlah Guru -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-green-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 font-medium text-sm uppercase tracking-wide">Jumlah Guru</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.teachers.toLocaleString() }}</p>
              <p class="text-sm text-blue-600 mt-1 font-medium">+3 baru ditambahkan</p>
            </div>
            <div class="text-green-500 p-3 bg-green-100 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2M12 12a4 4 0 100-8 4 4 0 000 8z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Card: Kelas Aktif -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-yellow-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 font-medium text-sm uppercase tracking-wide">Kelas Aktif</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.activeClasses.toLocaleString() }}</p>
              <p class="text-sm text-yellow-600 mt-1 font-medium">Semua kelas berjalan</p>
            </div>
            <div class="text-yellow-500 p-3 bg-yellow-100 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                <path d="M12 14l6.16-3.422a.998.998 0 00.414-.99l-2.062-8.248a.998.998 0 00-1.928-.275L12 5.094l-4.144-2.61a.998.998 0 00-1.928.275l-2.062 8.248a.998.998 0 00.414.99L12 14z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Card: Tugas Terkirim -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-red-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 font-medium text-sm uppercase tracking-wide">Tugas Terkirim</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.submittedAssignments }}%</p>
              <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-green-600 h-2 rounded-full transition-all duration-300" :style="{ width: stats.submittedAssignments + '%' }"></div>
              </div>
            </div>
            <div class="text-red-500 p-3 bg-red-100 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>
      </section>

      <!-- Line Chart Section -->
      <section class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold text-gray-900">Pertumbuhan Jumlah Siswa</h2>
          <button @click="refreshChartData" class="px-3 py-1 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 transition-colors">
            Update Chart
          </button>
        </div>
        <div v-if="chartLoaded" class="relative h-80">
          <Line :data="chartData" :options="chartOptions" :key="chartKey" />
        </div>
        <div v-else class="flex items-center justify-center h-80 text-gray-500">
          <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
            Loading chart...
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'; // Jika Sidebar butuh, tapi ini untuk contoh
import Sidebar from '@/Components/common/Sidebar.vue';
import { Line } from 'vue-chartjs';
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
} from 'chart.js';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
);

export default {
  name: 'AdminDashboard',
  components: {
    Sidebar,
    Line,
  },
  data() {
    return {
      currentDate: new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
      stats: {
        students: 2345,
        teachers: 128,
        activeClasses: 45,
        submittedAssignments: 98,
      },
      chartLoaded: false,
      chartKey: 0, // Untuk force re-render
      chartData: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [
          {
            label: 'Jumlah Siswa',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 2,
            data: [2200, 2250, 2300, 2280, 2350, 2400, 2380, 2450, 2500, 2480, 2550, 2600],
            fill: true,
            tension: 0.4,
          },
        ],
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Pertumbuhan Siswa per Bulan (2025)',
            font: {
              size: 16,
            },
          },
        },
        scales: {
          y: {
            beginAtZero: false,
            title: {
              display: true,
              text: 'Jumlah Siswa',
            },
          },
          x: {
            title: {
              display: true,
              text: 'Bulan',
            },
          },
        },
      },
    };
  },
  mounted() {
    // Delay kecil untuk simulate loading
    this.$nextTick(() => {
      this.chartLoaded = true;
    });
  },
  methods: {
    refreshData() {
      // Simulate data refresh
      this.stats.students += Math.floor(Math.random() * 10);
      this.stats.teachers += Math.floor(Math.random() * 2);
      this.stats.activeClasses += Math.floor(Math.random() * 3);
      this.stats.submittedAssignments = Math.min(100, this.stats.submittedAssignments + Math.floor(Math.random() * 5) - 2);
    },
    refreshChartData() {
      // Update chart data secara random
      this.chartData.datasets[0].data = this.chartData.datasets[0].data.map((val) => 
        val + Math.floor(Math.random() * 100 - 50)
      );
      this.chartKey++; // Force re-render
      this.$forceUpdate(); // Pastikan Vue update
    },
  },
};
</script>

<style scoped>
/* Tambahan style jika diperlukan, tapi Tailwind sudah handle */
</style>