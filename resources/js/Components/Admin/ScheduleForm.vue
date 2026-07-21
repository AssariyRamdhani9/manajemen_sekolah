<template>
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 sm:p-8">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
        <div>
          <h3 class="text-lg font-bold text-slate-900">
            {{ schedule ? 'Edit Jadwal Mengajar' : 'Tambah Jadwal Mengajar Baru' }}
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Tentukan kelas, mata pelajaran, hari, dan jam mengajar</p>
        </div>
        <button 
          @click="$emit('close')" 
          class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="saveSchedule" class="space-y-4 text-xs font-medium text-slate-700">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block mb-1 font-semibold text-slate-700">Kelas</label>
            <select
              v-model="form.class_id"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
              required
            >
              <option value="" disabled>Pilih Kelas</option>
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
            </select>
          </div>

          <div>
            <label class="block mb-1 font-semibold text-slate-700">Mata Pelajaran</label>
            <select
              v-model="form.subject_id"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
              required
            >
              <option value="" disabled>Pilih Mata Pelajaran</option>
              <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Hari Mengajar</label>
          <select
            v-model="form.day_of_week"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
            required
          >
            <option value="" disabled>Pilih Hari</option>
            <option>Senin</option>
            <option>Selasa</option>
            <option>Rabu</option>
            <option>Kamis</option>
            <option>Jumat</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block mb-1 font-semibold text-slate-700">Waktu Mulai</label>
            <input
              type="time"
              v-model="form.start_time"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
              required
            />
          </div>

          <div>
            <label class="block mb-1 font-semibold text-slate-700">Waktu Selesai</label>
            <input
              type="time"
              v-model="form.end_time"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
              required
            />
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 mt-6">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-all"
          >
            Batal
          </button>
          <button
            type="submit"
            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-600/20 transition-all"
          >
            Simpan Jadwal
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  schedule: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'schedule-saved'])

const form = reactive({
  class_id: '',
  subject_id: '',
  day_of_week: '',
  start_time: '',
  end_time: '',
})

const classes = ref([])
const subjects = ref([])
const currentTeacherId = ref(null)

const saveSchedule = async () => {
  if (!currentTeacherId.value) {
    alert('ID Guru belum dimuat. Tidak dapat menyimpan jadwal.')
    return
  }
  
  try {
    const payload = { 
      ...form, 
      teacher_id: currentTeacherId.value 
    }
    
    if (props.schedule) {
      await axios.put(`/api/guru/schedules/${props.schedule.id}`, payload)
    } else {
      await axios.post('/api/guru/schedules', payload)
    }
    emit('schedule-saved')
    emit('close')
  } catch (error) {
    console.error('Gagal menyimpan jadwal:', error)
    const errorMessage = error.response?.data?.message || 'Gagal menyimpan jadwal. Silakan periksa data Anda.'
    alert(errorMessage)
  }
}

watch(() => props.schedule, (newSchedule) => {
  if (newSchedule && newSchedule.class_subject_teacher) {
    form.class_id = newSchedule.class_subject_teacher.class_id
    form.subject_id = newSchedule.class_subject_teacher.subject_id
    form.day_of_week = newSchedule.day_of_week || ''
    form.start_time = newSchedule.start_time || ''
    form.end_time = newSchedule.end_time || ''
  } else {
    form.class_id = ''
    form.subject_id = ''
    form.day_of_week = ''
    form.start_time = ''
    form.end_time = ''
  }
}, { immediate: true })

onMounted(async () => {
  try {
    const [userResponse, classesResponse, subjectsResponse] = await Promise.all([
      axios.get('/api/user'),
      axios.get('/api/guru/classes'),
      axios.get('/api/guru/subjects'),
    ])

    const userData = userResponse.data
    let teacherIdFound = null
    if (userData && userData.teacher && userData.teacher.id) {
      teacherIdFound = userData.teacher.id
    } else if (userData && userData.teacher_id) {
      teacherIdFound = userData.teacher_id 
    } else if (userData && userData.id) {
      teacherIdFound = userData.id
    }
    currentTeacherId.value = teacherIdFound

    classes.value = classesResponse.data
    subjects.value = subjectsResponse.data
  } catch (error) {
    console.error('Gagal memuat data awal:', error)
  }
})
</script>
