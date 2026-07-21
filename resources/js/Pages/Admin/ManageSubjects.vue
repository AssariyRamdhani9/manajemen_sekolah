<template>
  <AppLayout role="admin">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Mata Pelajaran</h1>
        <p class="text-xs text-slate-500 mt-1">Daftar mata pelajaran dan mata kuliah akademik sekolah.</p>
      </div>

      <button
        @click="openFormForNewSubject"
        class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-600/20 flex items-center justify-center gap-2 active:scale-95 w-fit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Tambah Mata Pelajaran</span>
      </button>
    </div>

    <!-- Table Card Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <!-- Search Filter Bar -->
      <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between gap-4">
        <div class="relative w-full max-w-xs">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari mata pelajaran..."
            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
          />
        </div>

        <span class="text-xs text-slate-400 font-medium">Total: {{ filteredSubjects.length }} Mata Pelajaran</span>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">Nama Mata Pelajaran</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="subject in filteredSubjects" :key="subject.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4 font-bold text-slate-900 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 font-bold flex items-center justify-center text-xs border border-amber-100">
                  {{ subject.name ? subject.name[0].toUpperCase() : 'M' }}
                </div>
                <span>{{ subject.name }}</span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="editSubject(subject)"
                    class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                    title="Edit Data"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteSubject(subject.id)"
                    class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                    title="Hapus Data"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredSubjects.length === 0">
              <td colspan="2" class="px-6 py-12 text-center text-slate-400 text-xs">
                Tidak ada data mata pelajaran ditemukan.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form -->
    <SubjectForm
      v-if="showForm"
      :subject="selectedSubject"
      @close="closeForm"
      @subject-saved="fetchSubjects"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import SubjectForm from '@/Components/Admin/SubjectForm.vue'

const subjects = ref([])
const searchQuery = ref('')
const showForm = ref(false)
const selectedSubject = ref(null)

const filteredSubjects = computed(() => {
  if (!searchQuery.value.trim()) return subjects.value
  const q = searchQuery.value.toLowerCase()
  return subjects.value.filter(s => s.name && s.name.toLowerCase().includes(q))
})

const openFormForNewSubject = () => {
  selectedSubject.value = null
  showForm.value = true
}

const editSubject = (subject) => {
  selectedSubject.value = subject
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  selectedSubject.value = null
}

const fetchSubjects = async () => {
  try {
    const response = await axios.get('/api/admin/subjects')
    subjects.value = response.data
  } catch (error) {
    console.error('Gagal mengambil data mata pelajaran:', error)
  }
}

const deleteSubject = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')) {
    try {
      await axios.delete(`/api/admin/subjects/${id}`)
      fetchSubjects()
    } catch (error) {
      console.error('Gagal menghapus mata pelajaran:', error)
    }
  }
}

onMounted(() => {
  fetchSubjects()
})
</script>
