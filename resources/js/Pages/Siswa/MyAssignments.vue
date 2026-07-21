<template>
  <AppLayout role="siswa">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tugas Pelajaran Saya</h1>
        <p class="text-xs text-slate-500 mt-1">Daftar tugas rumah, tenggat waktu pengumpulan, dan status jawaban.</p>
      </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">Judul Tugas</th>
              <th class="px-6 py-3.5">Mata Pelajaran</th>
              <th class="px-6 py-3.5">Tenggat Waktu</th>
              <th class="px-6 py-3.5">Status Pengumpulan</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="assignment in assignments" :key="assignment.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4 font-bold text-slate-900">{{ assignment.title }}</td>
              <td class="px-6 py-4 text-slate-500">
                {{ assignment.class_subject_teacher?.subject?.name || '-' }}
              </td>
              <td class="px-6 py-4 text-slate-500 font-mono">{{ assignment.due_date }}</td>
              <td class="px-6 py-4">
                <span 
                  class="px-2.5 py-1 rounded-full text-[11px] font-semibold border"
                  :class="{
                    'bg-emerald-50 text-emerald-700 border-emerald-200': assignment.submissions && assignment.submissions.length > 0,
                    'bg-rose-50 text-rose-700 border-rose-200': (!assignment.submissions || assignment.submissions.length === 0) && isOverdue(assignment.due_date),
                    'bg-amber-50 text-amber-700 border-amber-200': (!assignment.submissions || assignment.submissions.length === 0) && !isOverdue(assignment.due_date)
                  }"
                >
                  {{ getStatus(assignment) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-3">
                  <a 
                    v-if="assignment.file_url" 
                    :href="assignment.file_url" 
                    target="_blank" 
                    class="text-indigo-600 hover:text-indigo-800 font-semibold"
                  >
                    Lampiran
                  </a>
                  <button 
                    @click="openSubmissionForm(assignment.id)" 
                    class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-[11px] transition-all shadow-sm"
                  >
                    {{ assignment.submissions && assignment.submissions.length > 0 ? 'Perbarui Jawaban' : 'Unggah Tugas' }}
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="assignments.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                Belum ada tugas yang diberikan untuk Anda saat ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Submission Modal -->
    <SubmissionForm 
      v-if="showForm" 
      :assignment-id="selectedAssignmentId" 
      @close="closeForm" 
      @saved="fetchAssignments" 
    />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import SubmissionForm from '@/Components/Siswa/SubmissionForm.vue'

const assignments = ref([])
const showForm = ref(false)
const selectedAssignmentId = ref(null)

const fetchAssignments = async () => {
  try {
    const response = await axios.get('/api/siswa/assignments')
    assignments.value = response.data
  } catch (error) {
    console.error('Gagal mengambil data tugas:', error)
  }
}

const openSubmissionForm = (assignmentId) => {
  selectedAssignmentId.value = assignmentId
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  selectedAssignmentId.value = null
}

const isOverdue = (dueDate) => {
  if (!dueDate) return false
  return new Date(dueDate) < new Date()
}

const getStatus = (assignment) => {
  if (assignment.submissions && assignment.submissions.length > 0) {
    return 'Sudah Dikumpulkan'
  }
  if (isOverdue(assignment.due_date)) {
    return 'Terlambat'
  }
  return 'Belum Dikumpulkan'
}

onMounted(() => {
  fetchAssignments()
})
</script>