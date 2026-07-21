<template>
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-md overflow-hidden p-6 sm:p-8">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
        <div>
          <h3 class="text-lg font-bold text-slate-900">Pengumpulan Tugas Siswa</h3>
          <p class="text-xs text-slate-500 mt-0.5">Unggah berkas lembar jawaban atau tugas Anda</p>
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
      <form @submit.prevent="submitAssignment" class="space-y-4 text-xs font-medium text-slate-700">
        <div>
          <label class="block mb-1 font-semibold text-slate-700">Pilih Berkas Berkas Tugas (PDF, Docx, Zip)</label>
          <input 
            type="file" 
            @change="onFileChange" 
            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 cursor-pointer"
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
            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold shadow-md shadow-sky-600/20 transition-all"
          >
            Kirimkan Tugas
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import axios from 'axios'

const props = defineProps({
  assignmentId: {
    type: Number,
    required: true,
  },
})

const emit = defineEmits(['close', 'saved'])

const form = reactive({
  assignment_id: props.assignmentId,
  file: null,
})

const onFileChange = (e) => {
  form.file = e.target.files[0]
}

const submitAssignment = async () => {
  const formData = new FormData()
  formData.append('assignment_id', form.assignment_id)
  formData.append('file', form.file)

  try {
    await axios.post('/api/student/submissions', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    emit('saved')
    emit('close')
  } catch (error) {
    console.error('Gagal mengunggah tugas:', error)
    alert('Gagal mengunggah tugas. Pastikan semua field terisi dan format file benar.')
  }
}
</script>