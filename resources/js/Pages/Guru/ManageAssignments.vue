<template>
  <AppLayout role="guru">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Tugas Pelajaran</h1>
        <p class="text-xs text-slate-500 mt-1">Buat penugasan baru dan atur batas waktu pengumpulan.</p>
      </div>

      <button
        @click="showForm = true"
        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 active:scale-95 w-fit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Beri Tugas Baru</span>
      </button>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">Judul Tugas</th>
              <th class="px-6 py-3.5">Kelas Target</th>
              <th class="px-6 py-3.5">Mata Pelajaran</th>
              <th class="px-6 py-3.5">Tenggat Waktu</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="assignment in assignments" :key="assignment.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4 font-bold text-slate-900">{{ assignment.title }}</td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  {{ assignment.class_subject_teacher?.school_class?.name || 'N/A' }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-500 font-medium">
                {{ assignment.class_subject_teacher?.subject?.name || 'N/A' }}
              </td>
              <td class="px-6 py-4 font-mono text-slate-500">{{ assignment.due_date }}</td>
              <td class="px-6 py-4 text-right">
                <button 
                  @click="deleteAssignment(assignment.id)" 
                  class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                  title="Hapus Tugas"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </td>
            </tr>
            <tr v-if="assignments.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                Belum ada tugas yang diberikan kepada siswa.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Assignment Form Modal -->
    <AssignmentForm v-if="showForm" @close="closeForm" @saved="fetchAssignments" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import AssignmentForm from '@/Components/Guru/AssignmentForm.vue'

const assignments = ref([])
const showForm = ref(false)

const closeForm = () => {
  showForm.value = false
}

const fetchAssignments = async () => {
  try {
    const response = await axios.get('/api/guru/assignments')
    assignments.value = response.data
  } catch (error) {
    console.error('Gagal mengambil data tugas:', error)
  }
}

const deleteAssignment = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
    try {
      await axios.delete(`/api/guru/assignments/${id}`)
      fetchAssignments()
    } catch (error) {
      console.error('Gagal menghapus tugas:', error)
      alert('Gagal menghapus tugas.')
    }
  }
}

onMounted(() => {
  fetchAssignments()
})
</script>