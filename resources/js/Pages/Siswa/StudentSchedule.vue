<template>
  <AppLayout role="siswa">
    <!-- Header Page -->
    <div class="mb-8">
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Jadwal Pelajaran Saya</h1>
      <p class="text-xs text-slate-500 mt-1">Jadwal mingguan mata pelajaran dan ruangan kelas Anda.</p>
    </div>

    <div v-if="loading" class="bg-white rounded-3xl p-12 text-center text-slate-400 text-xs border border-slate-200/80">
      <div class="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
      Memuat jadwal pelajaran...
    </div>

    <div v-else-if="error" class="bg-rose-50 border border-rose-200 text-rose-600 rounded-3xl p-6 text-xs text-center font-medium">
      {{ error }}
    </div>

    <div v-else class="space-y-6">
      <div 
        v-for="(schedulesList, day) in groupedSchedules" 
        :key="day" 
        class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm"
      >
        <h2 class="text-base font-extrabold text-indigo-900 pb-3 border-b border-slate-100 mb-4 flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
          <span>Hari {{ day }}</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="schedule in schedulesList" 
            :key="schedule.id" 
            class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 hover:border-indigo-200 hover:bg-white hover:shadow-md transition-all space-y-2 text-xs"
          >
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-900 text-sm">
                {{ schedule.class_subject_teacher?.subject?.name || 'Mata Pelajaran' }}
              </span>
              <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-semibold text-[11px] border border-indigo-100">
                {{ schedule.start_time ? schedule.start_time.substring(0, 5) : '' }} - {{ schedule.end_time ? schedule.end_time.substring(0, 5) : '' }}
              </span>
            </div>

            <p class="text-slate-500 flex items-center gap-1.5">
              <span>👨‍🏫 Guru:</span>
              <strong class="text-slate-700">{{ schedule.class_subject_teacher?.teacher?.user?.full_name || 'Pengajar' }}</strong>
            </p>

            <p class="text-slate-500 flex items-center gap-1.5">
              <span>🏫 Ruang:</span>
              <span class="font-semibold text-slate-700">{{ schedule.room || 'Utama' }}</span>
            </p>
          </div>
        </div>
      </div>

      <div v-if="Object.keys(groupedSchedules).length === 0" class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-400 text-xs">
        Belum ada jadwal pelajaran terdaftar.
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const schedules = ref([])
const loading = ref(true)
const error = ref(null)

const fetchStudentSchedule = async () => {
  try {
    const response = await axios.get('/api/siswa/schedules')
    schedules.value = response.data
  } catch (err) {
    error.value = 'Gagal memuat jadwal. Silakan coba lagi.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const groupedSchedules = computed(() => {
  return schedules.value.reduce((groups, schedule) => {
    const day = schedule.day_of_week
    if (!groups[day]) {
      groups[day] = []
    }
    groups[day].push(schedule)
    return groups;
  }, {})
})

onMounted(() => {
  fetchStudentSchedule()
})
</script>