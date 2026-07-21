<template>
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 sm:p-8">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
        <div>
          <h3 class="text-lg font-bold text-slate-900">Buat Pengumuman Baru</h3>
          <p class="text-xs text-slate-500 mt-0.5">Publikasikan pengumuman resmi ke portal sekolah</p>
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
      <form @submit.prevent="saveAnnouncement" class="space-y-4 text-xs font-medium text-slate-700">
        <div>
          <label class="block mb-1 font-semibold text-slate-700">Judul Pengumuman</label>
          <input
            type="text"
            v-model="form.title"
            placeholder="Judul pengumuman..."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Isi Pengumuman</label>
          <textarea
            v-model="form.content"
            rows="4"
            placeholder="Informasi pengumuman selengkapnya..."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required
          ></textarea>
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Target Audiens</label>
          <select
            v-model="form.target_audience"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
          >
            <option value="all_school">Seluruh Sekolah</option>
            <option value="specific_class">Kelas Tertentu</option>
            <option value="specific_subject">Mata Pelajaran Tertentu</option>
          </select>
        </div>

        <div v-if="form.target_audience === 'specific_class'">
          <label class="block mb-1 font-semibold text-slate-700">Pilih Kelas</label>
          <select
            v-model="form.class_id"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required
          >
            <option value="" disabled>Pilih Kelas</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>

        <div v-if="form.target_audience === 'specific_subject'">
          <label class="block mb-1 font-semibold text-slate-700">Pilih Mata Pelajaran</label>
          <select
            v-model="form.subject_id"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required
          >
            <option value="" disabled>Pilih Mata Pelajaran</option>
            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
          </select>
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
            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-600/20 transition-all"
          >
            Publikasikan
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
  title: '',
  content: '',
  target_audience: 'all_school',
  class_id: null,
  subject_id: null,
})

const classes = ref([])
const subjects = ref([])

const fetchInitialData = async () => {
  try {
    const classesResponse = await axios.get('/api/guru/classes')
    classes.value = classesResponse.data

    const subjectsResponse = await axios.get('/api/guru/subjects')
    subjects.value = subjectsResponse.data
  } catch (error) {
    console.error('Gagal mengambil data kelas/mata pelajaran:', error)
  }
}

const saveAnnouncement = async () => {
  try {
    await axios.post('/api/announcements', form)
    emit('saved')
    emit('close')
  } catch (error) {
    console.error('Gagal mempublikasikan pengumuman:', error)
    alert('Gagal mempublikasikan pengumuman. Pastikan semua field terisi dengan benar.')
  }
}

onMounted(() => {
  fetchInitialData()
})
</script>