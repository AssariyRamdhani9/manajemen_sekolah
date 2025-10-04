<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <Sidebar role="teacher" />

    <!-- Main Content -->
    <main class="flex-grow p-8 sm:p-12 transition-all duration-300">
      <!-- Header -->
      <header class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 leading-tight">Dashboard Guru</h1>
          <p class="text-lg text-gray-600 mt-2">Ringkasan kegiatan mengajar hari ini dan akses cepat untuk aktivitas rutin.</p>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-sm text-gray-500">
            <div class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V8H3v11a2 2 0 002 2z"/>
              </svg>
              {{ todayFull }}
            </div>
          </div>

          <button @click="refreshAll" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6"/>
            </svg>
            Refresh
          </button>
        </div>
      </header>

      <!-- Top Summary -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-indigo-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Jumlah Kelas</p>
          <p class="text-3xl font-bold text-gray-900 mt-1">{{ summary.classes }}</p>
          <p class="text-sm text-gray-400 mt-1">Kelas aktif yang Anda ampu</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-blue-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Jumlah Siswa</p>
          <p class="text-3xl font-bold text-gray-900 mt-1">{{ summary.students }}</p>
          <p class="text-sm text-gray-400 mt-1">Total siswa di semua kelas</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-yellow-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tugas Belum Dinilai</p>
          <p class="text-3xl font-bold text-gray-900 mt-1">{{ summary.ungraded }}</p>
          <p class="text-sm text-yellow-600 mt-1">Perlu penilaian</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-pink-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pengumuman Baru</p>
          <p class="text-3xl font-bold text-gray-900 mt-1">{{ announcements.length }}</p>
          <p class="text-sm text-pink-600 mt-1">Pengumuman belum dibaca</p>
        </div>
      </section>

      <!-- Two-column: Schedule & Quick Actions + Announcements -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Jadwal Mengajar Hari Ini (kolom utama) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Jadwal Mengajar Hari Ini</h2>
            <div class="text-sm text-gray-500">{{ todayShort }}</div>
          </div>

          <div v-if="todaySchedule.length" class="space-y-3">
            <div v-for="item in todaySchedule" :key="item.id" class="flex items-center justify-between p-4 rounded-lg border hover:shadow-md transition">
              <div>
                <div class="text-sm text-gray-500">{{ item.time }}</div>
                <div class="text-lg font-semibold text-gray-800">{{ item.subject }}</div>
                <div class="text-sm text-gray-500">{{ item.className }} • Ruang {{ item.room }}</div>
              </div>
              <div class="flex flex-col items-end gap-2">
                <button @click="goToAttendance(item)" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">Absensi</button>
                <button @click="createAssignment(item)" class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">Buat Tugas</button>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-12 text-gray-500">
            Tidak ada jadwal untuk hari ini.
          </div>
        </div>

        <!-- Akses Cepat & Pengumuman (kolom kanan) -->
        <aside class="bg-white rounded-xl shadow-lg p-6 space-y-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Akses Cepat</h3>
            <div class="grid grid-cols-1 gap-3">
              <button @click="createAssignmentShortcut" class="w-full px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Buat Tugas Baru</button>
              <button @click="uploadMaterial" class="w-full px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Unggah Materi</button>
              <button @click="markAttendanceShortcut" class="w-full px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600">Catat Absensi</button>
              <button @click="newAnnouncement" class="w-full px-4 py-2 rounded-lg bg-pink-600 text-white hover:bg-pink-700">Buat Pengumuman Kelas</button>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Pengumuman Baru</h3>
            <div class="space-y-3 max-h-64 overflow-auto">
              <div v-for="ann in announcements" :key="ann.id" class="p-3 border rounded hover:bg-gray-50 transition">
                <div class="flex justify-between items-start">
                  <div>
                    <div class="font-semibold text-gray-800">{{ ann.title }}</div>
                    <div class="text-xs text-gray-500">{{ ann.time }}</div>
                    <div class="text-sm text-gray-700 mt-1">{{ ann.excerpt }}</div>
                  </div>
                  <div class="text-xs text-gray-400 ml-2">{{ ann.from }}</div>
                </div>
              </div>
              <div v-if="!announcements.length" class="text-gray-500 text-sm">Belum ada pengumuman baru.</div>
            </div>
          </div>
        </aside>
      </section>

      <!-- Footer / aktivitas singkat -->
      <section class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terakhir</h3>
        <ul class="space-y-3 text-sm text-gray-700">
          <li v-for="(act, idx) in recentActivities" :key="idx" class="flex justify-between">
            <div>{{ act.text }}</div>
            <div class="text-gray-400">{{ act.time }}</div>
          </li>
        </ul>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/common/Sidebar.vue';

// --- Dummy data (gantikan nanti dengan API) ---
const summary = reactive({
  classes: 4,
  students: 126,
  ungraded: 8,
});

const announcements = ref([
  { id: 1, title: 'Rapat Guru Jumat', time: '2 jam lalu', excerpt: 'Rapat evaluasi semester di ruang rapat 1', from: 'Kepala Sekolah' },
  { id: 2, title: 'Pengumpulan RPP', time: '1 hari lalu', excerpt: 'Segera kumpulkan RPP sebelum Senin', from: 'Koordinator' },
]);

const todaySchedule = ref([
  { id: 1, subject: 'Matematika', time: '07:30 - 08:30', className: 'X IPA 1', room: 'A101' },
  { id: 2, subject: 'Fisika', time: '09:00 - 10:30', className: 'XI IPA 2', room: 'B203' },
]);

const recentActivities = ref([
  { text: 'Menilai tugas Tugas Ulangan Matematika (X IPA 1)', time: '1 jam lalu' },
  { text: 'Mengunggah materi: Limit Fungsi', time: '3 jam lalu' },
  { text: 'Membuat pengumuman untuk XI IPA 2', time: 'Kemarin' },
]);

// --- Utility & reactive states ---
const chartLoaded = ref(false);
const chartKey = ref(0);

const today = new Date();
const todayFull = today.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
const todayShort = today.toLocaleDateString('id-ID');

// --- Actions (sementara simulate) ---
const refreshAll = () => {
  // simulate refresh
  summary.classes += 0;
  summary.students += Math.floor(Math.random() * 3);
  summary.ungraded = Math.max(0, summary.ungraded + Math.floor(Math.random() * 3 - 1));
  chartKey.value++;
};

const refreshData = () => refreshAll();

const goToAttendance = (item) => {
  // contoh: route ke halaman absensi kelas tertentu
  // router.visit(`/teacher/attendance/${item.id}`);
  alert(`Buka absensi untuk ${item.className} (${item.subject})`);
};

const createAssignment = (item) => {
  // contoh: buka modal atau halaman pembuatan tugas
  // router.visit('/teacher/assignments/create', { data: { classId: item.id }});
  alert(`Buat tugas untuk ${item.className} (${item.subject})`);
};

const createAssignmentShortcut = () => {
  alert('Membuka form pembuatan tugas baru');
};

const uploadMaterial = () => {
  alert('Membuka dialog upload materi');
};

const markAttendanceShortcut = () => {
  alert('Membuka panel absensi cepat');
};

const newAnnouncement = () => {
  alert('Membuka form pengumuman kelas');
};

// --- expose for template ---
const stats = summary;
</script>

<style scoped>
/* khusus jika perlu styling tambahan; Tailwind menangani mayoritas */
</style>
