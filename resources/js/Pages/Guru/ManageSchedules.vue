<template>
  <AppLayout role="guru">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Jadwal Mengajar</h1>
        <p class="text-xs text-slate-500 mt-1">Kelola hari, jam mengajar, kelas, dan mata pelajaran Anda.</p>
      </div>

      <button
        @click="openFormForNewSchedule"
        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 active:scale-95 w-fit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Tambah Jadwal Baru</span>
      </button>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">Hari</th>
              <th class="px-6 py-3.5">Waktu Mengajar</th>
              <th class="px-6 py-3.5">Kelas</th>
              <th class="px-6 py-3.5">Mata Pelajaran</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="schedule in schedules" :key="schedule.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4 font-bold text-slate-900">{{ schedule.day_of_week }}</td>
              <td class="px-6 py-4 font-mono text-slate-500">
                {{ schedule.start_time }} - {{ schedule.end_time }}
              </td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  {{ schedule.class_subject_teacher?.school_class?.name || 'N/A' }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-700 font-medium">
                {{ schedule.class_subject_teacher?.subject?.name || 'N/A' }}
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="editSchedule(schedule)"
                    class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                    title="Edit Data"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteSchedule(schedule.id)"
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
            <tr v-if="schedules.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                Belum ada jadwal mengajar yang terdaftar.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Schedule Form Modal -->
    <ScheduleForm
      v-if="showForm"
      :schedule="selectedSchedule"
      @close="closeForm"
      @schedule-saved="fetchSchedules"
    />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import ScheduleForm from '@/Components/Admin/ScheduleForm.vue'

const schedules = ref([])
const showForm = ref(false)
const selectedSchedule = ref(null)

const openFormForNewSchedule = () => {
  selectedSchedule.value = null
  showForm.value = true
}

const editSchedule = (schedule) => {
  selectedSchedule.value = schedule
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  selectedSchedule.value = null
}

const fetchSchedules = async () => {
  try {
    const response = await axios.get('/api/guru/schedules')
    schedules.value = response.data
  } catch (error) {
    console.error('Gagal mengambil jadwal:', error)
  }
}

const deleteSchedule = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
    try {
      await axios.delete(`/api/guru/schedules/${id}`)
      fetchSchedules()
    } catch (error) {
      console.error('Gagal menghapus jadwal:', error)
    }
  }
}

onMounted(() => {
  fetchSchedules()
})
</script>
