<template>
  <aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
    <div class="text-2xl font-bold mb-6">Sekolahku</div>
    <nav>
      <ul>
        <li v-for="link in links" :key="link.name" class="mb-2">
          <Link
            :href="link.path"
            class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200"
          >
            {{ link.name }}
          </Link>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'   // ✅ pakai Inertia Link

const props = defineProps({
  role: {
    type: String,
    required: true,
  },
})

const links = computed(() => {
  if (props.role === 'admin') {
    return [
      { name: 'Dashboard', path: '/dashboard/admin' },
      { name: 'Manajemen Siswa', path: '/admin/students' },
      { name: 'Manajemen Guru', path: '/admin/teachers' },
      { name: 'Manajemen Kelas', path: '/admin/classes' },
      { name: 'Manajemen Mata Pelajaran', path: '/admin/subjects' },
      { name: 'Pengumuman', path: '/admin/announcements' },
    ]
  } else if (props.role === 'teacher') {
    return [
      { name: 'Dashboard', path: '/dashboard/teacher' },
      { name: 'Manajemen Tugas', path: '/teacher/assignments' },
      { name: 'Manajemen Jadwal', path: '/teacher/schedules' },
      { name: 'Materi Pembelajaran', path: '/teacher/materials' },
      { name: 'Absensi', path: '/teacher/attendances' },
      { name: 'Pengumuman', path: '/teacher/announcements' },
      
    ]
  } else if (props.role === 'student') {
    return [
      { name: 'Dashboard', path: '/student/dashboard' },
      { name: 'Jadwal Saya', path: '/student/schedules' },
      { name: 'Tugas Saya', path: '/student/assignments' },
      { name: 'Materi Pelajaran', path: '/student/materials' },
      { name: 'Pengumuman', path: '/student/announcements' },
    ]
  }
  return []
})
</script>
