<template>
  <div class="min-h-screen flex bg-gray-50">
    <!-- Sidebar -->
    <Sidebar role="student" />

    <!-- Main Content -->
    <main class="flex-grow p-6 sm:p-10 transition-all duration-300">
      <!-- Header -->
      <header class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900">Dashboard Siswa</h1>
          <p class="text-gray-600 mt-2">Halo, {{ profile.name }}! Berikut ringkasan akademik kamu.</p>
        </div>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2 text-sm text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ currentDate }}
          </div>
        </div>
      </header>

      <!-- Ringkasan Akademik -->
      <section class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-blue-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <h3 class="text-sm font-medium text-gray-500">Kehadiran</h3>
          <p class="text-2xl font-bold mt-2">{{ attendance.present }}/{{ attendance.total }}</p>
          <p class="text-sm text-gray-500">Hadir dari total pertemuan</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-green-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <h3 class="text-sm font-medium text-gray-500">Tugas Mendekati Deadline</h3>
          <p class="text-2xl font-bold mt-2">{{ assignmentsDue.length }}</p>
          <p class="text-sm text-gray-500">Tugas harus segera dikumpulkan</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-yellow-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <h3 class="text-sm font-medium text-gray-500">Nilai Terbaru</h3>
          <p class="text-2xl font-bold mt-2">{{ latestGrades.length }}</p>
          <p class="text-sm text-gray-500">Nilai baru minggu ini</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-red-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <h3 class="text-sm font-medium text-gray-500">Pengumuman</h3>
          <p class="text-2xl font-bold mt-2">{{ announcements.length }}</p>
          <p class="text-sm text-gray-500">Pengumuman terbaru</p>
        </div>
      </section>

      <!-- Jadwal Hari Ini -->
      <section class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📅 Jadwal Hari Ini</h2>
        <ul class="divide-y divide-gray-200">
          <li v-for="(schedule, index) in todaySchedule" :key="index" class="py-3 flex justify-between items-center">
            <div>
              <p class="font-medium text-gray-800">{{ schedule.subject }}</p>
              <p class="text-sm text-gray-500">Kelas {{ schedule.class }} • {{ schedule.room }}</p>
            </div>
            <span class="text-sm text-gray-600">{{ schedule.time }}</span>
          </li>
        </ul>
      </section>

      <!-- Tugas Mendekati Deadline -->
      <section class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📌 Tugas Mendekati Tenggat</h2>
        <ul class="divide-y divide-gray-200">
          <li v-for="(task, index) in assignmentsDue" :key="index" class="py-3 flex justify-between items-center">
            <div>
              <p class="font-medium text-gray-800">{{ task.title }}</p>
              <p class="text-sm text-gray-500">Mata pelajaran: {{ task.subject }}</p>
            </div>
            <span class="text-sm text-red-600">Deadline: {{ task.deadline }}</span>
          </li>
        </ul>
      </section>

      <!-- Nilai Terbaru -->
      <section class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📝 Nilai Terbaru</h2>
        <ul class="divide-y divide-gray-200">
          <li v-for="(grade, index) in latestGrades" :key="index" class="py-3 flex justify-between items-center">
            <div>
              <p class="font-medium text-gray-800">{{ grade.subject }}</p>
              <p class="text-sm text-gray-500">{{ grade.assignment }}</p>
            </div>
            <span class="text-sm font-bold text-green-600">{{ grade.score }}</span>
          </li>
        </ul>
      </section>

      <!-- Akses Cepat -->
      <section class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">⚡ Akses Cepat</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <button class="p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600">📖 Lihat Tugas</button>
          <button class="p-4 bg-green-500 text-white rounded-lg hover:bg-green-600">⬆️ Unggah Tugas</button>
          <button class="p-4 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">📚 Materi</button>
          <button class="p-4 bg-red-500 text-white rounded-lg hover:bg-red-600">📢 Pengumuman</button>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import Sidebar from '@/Components/common/Sidebar.vue';

export default {
  name: 'StudentDashboard',
  components: { Sidebar },
  data() {
    return {
      currentDate: new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
      profile: {
        name: 'Budi Santoso',
        class: 'XI IPA 2',
      },
      attendance: {
        present: 40,
        total: 45,
      },
      todaySchedule: [
        { subject: 'Matematika', class: 'XI IPA 2', room: 'Ruang 201', time: '07:00 - 08:30' },
        { subject: 'Fisika', class: 'XI IPA 2', room: 'Ruang 202', time: '09:00 - 10:30' },
        { subject: 'Bahasa Inggris', class: 'XI IPA 2', room: 'Ruang 105', time: '11:00 - 12:30' },
      ],
      assignmentsDue: [
        { title: 'PR Matematika Bab 5', subject: 'Matematika', deadline: '12 Sep 2025' },
        { title: 'Laporan Praktikum Fisika', subject: 'Fisika', deadline: '13 Sep 2025' },
      ],
      latestGrades: [
        { subject: 'Matematika', assignment: 'Ulangan Bab 4', score: 85 },
        { subject: 'Bahasa Inggris', assignment: 'Essay', score: 90 },
      ],
      announcements: [
        { id: 1, title: 'Libur Idul Adha 17 Sep 2025' },
        { id: 2, title: 'Try Out Ujian Nasional 25 Sep 2025' },
      ],
    };
  },
};
</script>

<style scoped>
/* tambahan opsional */
</style>
