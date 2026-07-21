<template>
  <AppLayout role="siswa">
    <!-- Header Banner Siswa -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-sky-950 via-blue-900 to-slate-900 p-6 sm:p-8 rounded-3xl text-white shadow-xl border border-sky-900/40 relative overflow-hidden">
      <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>

      <div class="relative z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 text-xs font-semibold mb-3">
          <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
          Portal Pembelajaran Siswa
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ profile.name }}! 👋</h1>
        <p class="text-sky-100 text-sm mt-1">Kelas: <span class="font-semibold text-white">{{ profile.class }}</span> • Pantau jadwal harian dan tugas sekolah kamu di sini.</p>
      </div>

      <div class="relative z-10">
        <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/10 text-xs text-slate-200 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V8H3v11a2 2 0 002 2z" />
          </svg>
          {{ currentDate }}
        </div>
      </div>
    </div>

    <!-- Stats Cards Siswa -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Persentase Kehadiran</p>
        <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">{{ attendance.present }}/{{ attendance.total }}</h3>
        <p class="text-xs text-emerald-600 font-semibold mt-2">Target kehadiran terpenuhi</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tugas Mendekati Tenggat</p>
        <h3 class="text-3xl font-extrabold text-rose-600 mt-2 tracking-tight">{{ assignmentsDue.length }}</h3>
        <p class="text-xs text-rose-500 font-semibold mt-2">Segera kumpulkan</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nilai Terbaru Ditambahkan</p>
        <h3 class="text-3xl font-extrabold text-sky-600 mt-2 tracking-tight">{{ latestGrades.length }}</h3>
        <p class="text-xs text-slate-500 mt-2">Evaluasi tugas terbaru</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengumuman Sekolah</p>
        <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">{{ announcements.length }}</h3>
        <p class="text-xs text-slate-500 mt-2">Informasi penting</p>
      </div>
    </section>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Schedule Today -->
      <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span>📅 Jadwal Pelajaran Hari Ini</span>
        </h2>
        <div class="space-y-3">
          <div 
            v-for="(schedule, index) in todaySchedule" 
            :key="index" 
            class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:bg-white hover:border-sky-200 hover:shadow-md transition-all flex items-center justify-between"
          >
            <div>
              <h4 class="font-bold text-slate-800 text-sm">{{ schedule.subject }}</h4>
              <p class="text-xs text-slate-500 mt-0.5">Kelas: {{ schedule.class }} • {{ schedule.room }}</p>
            </div>
            <span class="px-3 py-1 rounded-xl bg-sky-50 text-sky-700 font-bold text-xs border border-sky-100">
              {{ schedule.time }}
            </span>
          </div>
        </div>
      </div>

      <!-- Quick Shortcuts & Announcements -->
      <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
          <h2 class="text-lg font-bold text-slate-900 mb-4">Akses Cepat Siswa</h2>
          <div class="space-y-3">
            <Link href="/student/assignments" class="w-full p-4 rounded-2xl bg-sky-50 hover:bg-sky-100/80 border border-sky-200/60 text-sky-900 font-bold text-xs flex items-center justify-between transition-all">
              <span>📖 Tugas & Respon Pengumpulan</span>
              <span>→</span>
            </Link>
            <Link href="/student/schedules" class="w-full p-4 rounded-2xl bg-indigo-50 hover:bg-indigo-100/80 border border-indigo-200/60 text-indigo-900 font-bold text-xs flex items-center justify-between transition-all">
              <span>📅 Jadwal Pelajaran Lengkap</span>
              <span>→</span>
            </Link>
            <Link href="/siswa/announcements" class="w-full p-4 rounded-2xl bg-purple-50 hover:bg-purple-100/80 border border-purple-200/60 text-purple-900 font-bold text-xs flex items-center justify-between transition-all">
              <span>📢 Papan Pengumuman</span>
              <span>→</span>
            </Link>
          </div>
        </div>

        <!-- Announcements Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Pengumuman Sekolah</h3>
            <Link href="/siswa/announcements" class="text-xs font-semibold text-sky-600 hover:underline">Lihat Semua</Link>
          </div>
          <div v-if="announcements.length" class="space-y-3">
            <div v-for="ann in announcements" :key="ann.id" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
              <div class="flex justify-between items-start">
                <h5 class="font-bold text-slate-800 text-xs">{{ ann.title }}</h5>
                <span class="text-[10px] text-slate-400 shrink-0 ml-2">{{ ann.created_at ? new Date(ann.created_at).toLocaleDateString('id-ID') : '' }}</span>
              </div>
              <p class="text-xs text-slate-600 mt-1 leading-relaxed line-clamp-2">{{ ann.content }}</p>
            </div>
          </div>
          <div v-else class="text-center py-6 text-slate-400 text-xs">
            Belum ada pengumuman baru.
          </div>
        </div>
      </div>
    </div>

    <!-- Assignments Due & Latest Grades Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Assignments Due -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span>📌 Tugas Mendekati Tenggat</span>
        </h2>
        <div class="space-y-3">
          <div v-for="(task, index) in assignmentsDue" :key="index" class="p-4 rounded-2xl border border-slate-200/80 bg-rose-50/30 flex items-center justify-between">
            <div>
              <h4 class="font-bold text-slate-800 text-sm">{{ task.title }}</h4>
              <p class="text-xs text-slate-500 mt-0.5">Mata Pelajaran: {{ task.subject }}</p>
            </div>
            <span class="px-3 py-1 rounded-xl bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200">
              Tenggat: {{ task.deadline }}
            </span>
          </div>
        </div>
      </div>

      <!-- Latest Grades -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span>📝 Nilai Terbaru</span>
        </h2>
        <div class="space-y-3">
          <div v-for="(grade, index) in latestGrades" :key="index" class="p-4 rounded-2xl border border-slate-200/80 bg-emerald-50/30 flex items-center justify-between">
            <div>
              <h4 class="font-bold text-slate-800 text-sm">{{ grade.subject }}</h4>
              <p class="text-xs text-slate-500 mt-0.5">{{ grade.assignment }}</p>
            </div>
            <span class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white font-extrabold text-sm shadow-sm">
              {{ grade.score }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  student: Object,
  user: Object,
})

const currentDate = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric',
})

const profile = ref({
  name: props.user?.full_name || 'Siswa',
  class: props.student?.class?.name || '-',
})

const attendance = ref({
  present: 0,
  total: 0,
})

const todaySchedule = ref([])
const assignmentsDue = ref([])
const latestGrades = ref([])
const announcements = ref([])

onMounted(async () => {
  try {
    const [scheduleRes, annRes] = await Promise.allSettled([
      axios.get('/api/siswa/schedules'),
      axios.get('/api/announcements'),
    ])

    if (scheduleRes.status === 'fulfilled' && Array.isArray(scheduleRes.value.data)) {
      todaySchedule.value = scheduleRes.value.data.map(item => ({
        subject: item.class_subject_teacher?.subject?.name || 'Mata Pelajaran',
        class: item.class_subject_teacher?.school_class?.name || '-',
        room: item.room || 'Ruang Kelas',
        time: `${item.start_time} - ${item.end_time}`,
      }))
    }

    if (annRes.status === 'fulfilled' && Array.isArray(annRes.value.data)) {
      announcements.value = annRes.value.data
    }
  } catch (e) {
    console.error('Gagal memuat data dashboard siswa:', e)
  }
})
</script>
