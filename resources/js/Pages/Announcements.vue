<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengumuman Sekolah</h1>
        <p class="text-xs text-slate-500 mt-1">Informasi penting dan pengumuman resmi akademik.</p>
      </div>

      <button
        v-if="userRole === 'admin' || userRole === 'teacher' || userRole === 'guru'"
        @click="showForm = true"
        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2 active:scale-95 w-fit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Buat Pengumuman Baru</span>
      </button>
    </div>

    <!-- Announcements List -->
    <div class="space-y-4 max-w-4xl">
      <div 
        v-for="announcement in announcements" 
        :key="announcement.id" 
        class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition-all"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
              Target: {{ formatTarget(announcement) }}
            </span>
            <h2 class="text-lg font-bold text-slate-900 mt-2">{{ announcement.title }}</h2>
          </div>

          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400 font-medium shrink-0">{{ formatDate(announcement.created_at) }}</span>
            <button
              v-if="userRole === 'admin' || (user && user.id === announcement.user_id)"
              @click="deleteAnnouncement(announcement.id)"
              class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors ml-2"
              title="Hapus Pengumuman"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>

        <p class="text-xs text-slate-600 mt-3 leading-relaxed whitespace-pre-line">{{ announcement.content }}</p>

        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] text-slate-500 font-medium">
          <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-[10px]">
            {{ announcement.user && announcement.user.full_name ? announcement.user.full_name[0] : 'A' }}
          </div>
          <span>Diposting oleh: <strong class="text-slate-700">{{ announcement.user ? announcement.user.full_name : 'Admin' }}</strong></span>
        </div>
      </div>

      <div v-if="!announcements.length" class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-400 text-xs">
        Belum ada pengumuman yang dipublikasikan saat ini.
      </div>
    </div>

    <AnnouncementForm v-if="showForm" @close="showForm = false" @saved="fetchAnnouncements" />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import AnnouncementForm from '@/Components/AnnouncementForm.vue'

const page = usePage()
const announcements = ref([])
const showForm = ref(false)

const user = computed(() => page.props.auth?.user || page.props.user || {})
const userRole = computed(() => user.value.role || 'siswa')

const formatTarget = (ann) => {
  if (ann.target_audience === 'all_school') return 'Seluruh Sekolah'
  if (ann.target_audience === 'specific_class') return `Kelas ${ann.class?.name || ''}`
  if (ann.target_audience === 'specific_subject') return `Mapel ${ann.subject?.name || ''}`
  return 'Semua'
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  try {
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch (e) {
    return dateStr
  }
}

const fetchAnnouncements = async () => {
  try {
    const response = await axios.get('/api/announcements')
    announcements.value = response.data
  } catch (error) {
    console.error('Gagal mengambil pengumuman:', error)
  }
}

const deleteAnnouncement = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')) {
    try {
      await axios.delete(`/api/announcements/${id}`)
      fetchAnnouncements()
    } catch (error) {
      console.error('Gagal menghapus pengumuman:', error)
    }
  }
}

onMounted(() => {
  fetchAnnouncements()
})
</script>