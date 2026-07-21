<template>
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-md overflow-hidden p-6 sm:p-8">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
        <div>
          <h3 class="text-lg font-bold text-slate-900">
            {{ classData ? 'Edit Data Kelas' : 'Tambah Kelas Baru' }}
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Atur nama kelas, jenjang, dan jurusan</p>
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
      <form @submit.prevent="saveClass" class="space-y-4 text-xs font-medium text-slate-700">
        <div>
          <label class="block mb-1 font-semibold text-slate-700">Nama Kelas</label>
          <input
            type="text"
            v-model="form.name"
            placeholder="Contoh: X IPA 1"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
            required
          />
          <p v-if="errors.name" class="mt-1 text-[11px] text-rose-500">{{ errors.name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block mb-1 font-semibold text-slate-700">Jenjang</label>
            <input
              type="text"
              v-model="form.level"
              placeholder="X, XI, XII"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
            />
          </div>

          <div>
            <label class="block mb-1 font-semibold text-slate-700">Jurusan</label>
            <input
              type="text"
              v-model="form.major"
              placeholder="IPA, IPS, dll."
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
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
            class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold shadow-md shadow-purple-600/20 transition-all"
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
  classData: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'class-saved'])

const form = reactive({
  name: '',
  level: '',
  major: '',
})

const errors = reactive({
  name: '',
})

const saveClass = async () => {
  try {
    errors.name = ''
    if (props.classData) {
      await axios.put(`/api/admin/classes/${props.classData.id}`, form)
    } else {
      await axios.post('/api/admin/classes', form)
    }
    emit('class-saved')
    emit('close')
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.name = error.response.data.errors.name ? error.response.data.errors.name[0] : ''
    } else {
      alert('Gagal menyimpan data. Pastikan nama kelas unik.')
    }
  }
}

watch(() => props.classData, (newClassData) => {
  if (newClassData) {
    form.name = newClassData.name || ''
    form.level = newClassData.level || ''
    form.major = newClassData.major || ''
  } else {
    form.name = ''
    form.level = ''
    form.major = ''
  }
}, { immediate: true })
</script>