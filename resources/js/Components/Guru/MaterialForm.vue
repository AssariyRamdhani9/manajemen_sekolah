<template>
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 sm:p-8">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
        <div>
          <h3 class="text-lg font-bold text-slate-900">Unggah Materi Pembelajaran</h3>
          <p class="text-xs text-slate-500 mt-0.5">Unggah bahan ajar atau slide modul untuk siswa</p>
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
      <form @submit.prevent="uploadMaterial" class="space-y-4 text-xs font-medium text-slate-700">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block mb-1 font-semibold text-slate-700">Kelas Target</label>
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
          <label class="block mb-1 font-semibold text-slate-700">Judul Materi</label>
          <input 
            type="text" 
            v-model="form.title" 
            placeholder="Contoh: Modul Bab 2 Trigonometri"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all" 
            required
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Pilih Berkas Berkas Materi (PDF, Doc, PPT, Zip)</label>
          <input 
            type="file" 
            @change="onFileChange" 
            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
            required
          />
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
            Unggah Berkas
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import axios from 'axios'

const emit = defineEmits(['close', 'saved'])

const form = reactive({
  class_id: '',
  subject_id: '',
  title: '',
  file: null,
})

const classes = ref([])
const subjects = ref([])

const onFileChange = (e) => {
  form.file = e.target.files[0]
}

const uploadMaterial = async () => {
  const formData = new FormData()
  formData.append('class_id', form.class_id)
  formData.append('subject_id', form.subject_id)
  formData.append('title', form.title)
  formData.append('file', form.file)

  try {
    await axios.post('/api/guru/materials', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    emit('saved')
    emit('close')
  } catch (error) {
    console.error('Gagal mengunggah materi:', error)
    alert('Gagal mengunggah materi. Pastikan semua field terisi.')
  }
}

const fetchInitialData = async () => {
  try {
    let teacherParam = ''
    try {
      const userResponse = await axios.get('/api/user')
      const userData = userResponse.data
      const teacherId = userData?.teacher?.id || userData?.teacher_id
      if (teacherId) {
        teacherParam = `?teacher_id=${teacherId}`
      }
    } catch (e) {
      // Abaikan error jika user info tidak tersedia
    }

    const [classesResponse, subjectsResponse] = await Promise.all([
      axios.get(`/api/guru/classes${teacherParam}`),
      axios.get(`/api/guru/subjects${teacherParam}`),
    ])

    classes.value = classesResponse.data
    subjects.value = subjectsResponse.data
  } catch (error) {
    console.error('Gagal mengambil data awal untuk form:', error)
  }
}

onMounted(() => {
  fetchInitialData()
})
</script>