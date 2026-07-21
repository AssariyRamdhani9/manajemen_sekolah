<template>
  <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-8 py-3.5 flex items-center justify-between transition-all duration-300">
    <!-- Left: Mobile Menu Toggle & Title/Search -->
    <div class="flex items-center gap-4">
      <button 
        @click="$emit('toggle-sidebar')"
        class="lg:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors"
        aria-label="Toggle Navigation"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- Page Search / Breadcrumb -->
      <div class="hidden sm:flex items-center gap-2 bg-slate-100/80 px-3.5 py-2 rounded-xl border border-slate-200/60 focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all w-64 md:w-80">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input 
          type="text" 
          placeholder="Cari fitur, siswa, atau jadwal..." 
          class="bg-transparent border-none p-0 text-xs sm:text-sm text-slate-700 placeholder-slate-400 focus:ring-0 focus:outline-none w-full"
        />
      </div>
    </div>

    <!-- Right: Notifications, Role Pill & User Menu -->
    <div class="flex items-center gap-3">
      <!-- Role Badge -->
      <span 
        class="px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border shadow-sm"
        :class="roleBadgeClasses"
      >
        {{ roleName }}
      </span>

      <!-- User Profile Dropdown / Info -->
      <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-600 to-violet-500 text-white font-bold flex items-center justify-center text-sm shadow-md shadow-indigo-500/20 ring-2 ring-white">
          {{ userInitials }}
        </div>
        <div class="hidden md:block text-left">
          <p class="text-xs font-bold text-slate-800 leading-tight">{{ userName }}</p>
          <p class="text-[11px] text-slate-500 leading-tight mt-0.5">{{ userEmail }}</p>
        </div>

        <button 
          @click="handleLogout" 
          title="Keluar" 
          class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200 ml-1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  role: {
    type: String,
    default: 'admin'
  }
})

defineEmits(['toggle-sidebar'])

const user = ref({
  full_name: 'Pengguna',
  email: 'user@sekolah.sch.id',
  role: props.role
})

onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (e) {
      console.error(e)
    }
  }
})

const userName = computed(() => user.value.full_name || 'Pengguna')
const userEmail = computed(() => user.value.email || 'user@sekolah.sch.id')
const userInitials = computed(() => {
  const name = userName.value.trim()
  if (!name) return 'U'
  const parts = name.split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.slice(0, 2).toUpperCase()
})

const roleName = computed(() => {
  const r = props.role || user.value.role
  if (r === 'admin') return 'Administrator'
  if (r === 'guru' || r === 'teacher') return 'Guru'
  if (r === 'siswa' || r === 'student') return 'Siswa'
  return 'User'
})

const roleBadgeClasses = computed(() => {
  const r = props.role || user.value.role
  if (r === 'admin') return 'bg-purple-50 text-purple-700 border-purple-200'
  if (r === 'guru' || r === 'teacher') return 'bg-emerald-50 text-emerald-700 border-emerald-200'
  if (r === 'siswa' || r === 'student') return 'bg-sky-50 text-sky-700 border-sky-200'
  return 'bg-slate-100 text-slate-700 border-slate-200'
})

const handleLogout = async () => {
  try {
    const token = localStorage.getItem('token')
    if (token) {
      await axios.post('/api/auth/logout', {}, {
        headers: { Authorization: `Bearer ${token}` }
      })
    }
  } catch (e) {
    console.error(e)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    window.location.href = '/login'
  }
}
</script>
