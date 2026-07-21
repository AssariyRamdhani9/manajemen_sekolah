<template>
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-md overflow-hidden p-6 sm:p-8">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
        <div>
          <h3 class="text-lg font-bold text-slate-900">
            {{ subject ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran Baru' }}
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Isi nama mata pelajaran kurikulum</p>
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
      <form @submit.prevent="saveSubject" class="space-y-4 text-xs font-medium text-slate-700">
        <div>
          <label class="block mb-1 font-semibold text-slate-700">Nama Mata Pelajaran</label>
          <input
            type="text"
            v-model="form.name"
            placeholder="Contoh: Matematika Peminatan"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
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
            class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold shadow-md shadow-amber-600/20 transition-all"
          >
            Simpan Data
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  subject: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'subject-saved'])

const form = reactive({
  name: '',
})

const saveSubject = async () => {
  try {
    if (props.subject) {
      await axios.put(`/api/admin/subjects/${props.subject.id}`, form)
    } else {
      await axios.post('/api/admin/subjects', form)
    }
    emit('subject-saved')
    emit('close')
  } catch (error) {
    console.error('Gagal menyimpan data mata pelajaran:', error)
    alert('Gagal menyimpan data. Pastikan nama mata pelajaran unik.')
  }
}

watch(() => props.subject, (newSubject) => {
  if (newSubject) {
    form.name = newSubject.name || ''
  } else {
    form.name = ''
  }
}, { immediate: true })
</script>