<template>
  <aside 
    class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col justify-between border-r border-slate-800/80 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none"
    :class="[isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']"
  >
    <!-- Top Brand Section -->
    <div>
      <div class="px-6 py-5 flex items-center justify-between border-b border-slate-800/60">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <div>
            <h1 class="font-extrabold text-white text-lg tracking-tight">Sekolahku</h1>
            <p class="text-[10px] text-indigo-400 font-medium uppercase tracking-wider">Academic Portal</p>
          </div>
        </div>

        <!-- Close button on Mobile -->
        <button 
          @click="$emit('close-sidebar')" 
          class="lg:hidden p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="p-4 space-y-1">
        <p class="px-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Menu Utama</p>
        
        <Link
          v-for="link in links"
          :key="link.name"
          :href="link.path"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group"
          :class="[
            isCurrentPath(link.path)
              ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-md shadow-indigo-600/30 font-semibold'
              : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60'
          ]"
        >
          <component 
            :is="link.icon" 
            class="h-5 w-5 transition-transform duration-200 group-hover:scale-110"
            :class="[isCurrentPath(link.path) ? 'text-white' : 'text-slate-400 group-hover:text-indigo-400']"
          />
          <span>{{ link.name }}</span>
        </Link>
      </nav>
    </div>

    <!-- Footer / System Status -->
    <div class="p-4 border-t border-slate-800/60">
      <div class="bg-slate-800/40 border border-slate-800 rounded-xl p-3 flex items-center gap-3">
        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
        <div>
          <p class="text-xs font-semibold text-slate-200">Sistem Aktif</p>
          <p class="text-[10px] text-slate-500">Tahun Ajaran 2025/2026</p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed, h } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  role: {
    type: String,
    required: true,
  },
  isOpen: {
    type: Boolean,
    default: true
  }
})

defineEmits(['close-sidebar'])

const page = usePage()

const isCurrentPath = (path) => {
  return page.url === path || page.url.startsWith(path + '/')
}

// Icon SVG helper components
const IconDashboard = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' })
])

const IconUsers = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 4.354a4 4 0 100 5.292M15 21h3a2 2 0 002-2V7a2 2 0 00-2-2H9.5L9 2H5a2 2 0 00-2 2v14a2 2 0 002 2h4.5l.5 2h4l.5-2H17z' })
])

const IconTeachers = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2M12 12a4 4 0 100-8 4 4 0 000 8z' })
])

const IconClasses = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 4h4' })
])

const IconSubjects = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })
])

const IconAnnounce = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z' })
])

const IconSchedule = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
])

const IconAssignments = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' })
])

const IconMaterials = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' })
])

const IconAttendance = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
])

const IconGrades = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' })
])

const links = computed(() => {
  const r = props.role
  if (r === 'admin') {
    return [
      { name: 'Dashboard', path: '/dashboard/admin', icon: IconDashboard },
      { name: 'Manajemen Siswa', path: '/admin/students', icon: IconUsers },
      { name: 'Manajemen Guru', path: '/admin/teachers', icon: IconTeachers },
      { name: 'Manajemen Kelas', path: '/admin/classes', icon: IconClasses },
      { name: 'Mata Pelajaran', path: '/admin/subjects', icon: IconSubjects },
      { name: 'Absensi Siswa', path: '/teacher/attendances', icon: IconAttendance },
      { name: 'Penilaian & Rapor', path: '/teacher/grades', icon: IconGrades },
      { name: 'Pengumuman', path: '/admin/announcements', icon: IconAnnounce },
    ]
  } else if (r === 'guru' || r === 'teacher') {
    return [
      { name: 'Dashboard', path: '/dashboard/teacher', icon: IconDashboard },
      { name: 'Jadwal Mengajar', path: '/teacher/schedules', icon: IconSchedule },
      { name: 'Absensi Siswa', path: '/teacher/attendances', icon: IconAttendance },
      { name: 'Penilaian & Rapor', path: '/teacher/grades', icon: IconGrades },
      { name: 'Manajemen Tugas', path: '/teacher/assignments', icon: IconAssignments },
      { name: 'Materi Pembelajaran', path: '/teacher/materials', icon: IconMaterials },
      { name: 'Pengumuman', path: '/teacher/announcements', icon: IconAnnounce },
    ]
  } else if (r === 'siswa' || r === 'student') {
    return [
      { name: 'Dashboard', path: '/dashboard/siswa', icon: IconDashboard },
      { name: 'Jadwal Saya', path: '/student/schedules', icon: IconSchedule },
      { name: 'Tugas Saya', path: '/student/assignments', icon: IconAssignments },
      { name: 'Rapor Saya', path: '/student/report', icon: IconGrades },
      { name: 'Pengumuman', path: '/siswa/announcements', icon: IconAnnounce },
    ]
  }
  return []
})
</script>
