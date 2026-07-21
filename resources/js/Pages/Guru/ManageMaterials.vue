<template>
  <AppLayout role="guru">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Materi Pembelajaran</h1>
        <p class="text-xs text-slate-500 mt-1">Unggah modul, slide, dan bahan ajar untuk kelas Anda.</p>
      </div>

      <button
        @click="showForm = true"
        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 active:scale-95 w-fit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
        </svg>
        <span>Unggah Materi Baru</span>
      </button>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">Judul Materi</th>
              <th class="px-6 py-3.5">Kelas</th>
              <th class="px-6 py-3.5">Mata Pelajaran</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="material in materials" :key="material.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4 font-bold text-slate-900">{{ material.title }}</td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  {{ material.class_subject_teacher?.school_class?.name || 'N/A' }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-500 font-medium">
                {{ material.class_subject_teacher?.subject?.name || 'N/A' }}
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-3">
                  <a 
                    :href="material.file_url" 
                    target="_blank" 
                    class="text-indigo-600 hover:text-indigo-800 font-semibold"
                  >
                    Buka Berkas
                  </a>
                  <button 
                    @click="deleteMaterial(material.id)" 
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
            <tr v-if="materials.length === 0">
              <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-xs">
                Belum ada materi pembelajaran yang diunggah.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Upload Form Modal -->
    <MaterialForm v-if="showForm" @close="closeForm" @saved="fetchMaterials" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import MaterialForm from '@/Components/Guru/MaterialForm.vue'

const materials = ref([])
const showForm = ref(false)

const closeForm = () => {
  showForm.value = false
}

const fetchMaterials = async () => {
  try {
    const response = await axios.get('/api/guru/materials')
    materials.value = response.data
  } catch (error) {
    console.error('Gagal mengambil data materi:', error)
  }
}

const deleteMaterial = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus materi ini?')) {
    try {
      await axios.delete(`/api/guru/materials/${id}`)
      fetchMaterials()
    } catch (error) {
      console.error('Gagal menghapus materi:', error)
      alert('Gagal menghapus materi.')
    }
  }
}

onMounted(() => {
  fetchMaterials()
})
</script>