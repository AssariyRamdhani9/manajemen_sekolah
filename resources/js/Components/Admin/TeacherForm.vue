<template>
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 sm:p-8">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
        <div>
          <h3 class="text-lg font-bold text-slate-900">
            {{ teacher ? 'Edit Data Guru' : 'Tambah Guru Baru' }}
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Isi formulir berikut dengan informasi guru pengajar</p>
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
      <form @submit.prevent="saveTeacher" class="space-y-4 text-xs font-medium text-slate-700">
        <div>
          <label class="block mb-1 font-semibold text-slate-700">Nama Lengkap & Gelar</label>
          <input
            type="text"
            v-model="form.full_name"
            placeholder="Contoh: Budi Santoso, S.Pd."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
            required
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Email Utama</label>
          <input
            type="email"
            v-model="form.email"
            placeholder="guru@sekolah.sch.id"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all disabled:opacity-60"
            :readonly="!!teacher"
            required
          />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block mb-1 font-semibold text-slate-700">NIP</label>
            <input
              type="text"
              v-model="form.nip"
              placeholder="19850101..."
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
              required
            />
          </div>

          <div>
            <label class="block mb-1 font-semibold text-slate-700">Mata Pelajaran Utama</label>
            <select
              v-model="form.subject_taught_id"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
            >
              <option value="" disabled>Pilih Mata Pelajaran</option>
              <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
            </select>
          </div>
        </div>

        <div v-if="!teacher">
          <label class="block mb-1 font-semibold text-slate-700">Password Akun</label>
          <input
            type="password"
            v-model="form.password"
            placeholder="••••••••"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
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
            Simpan Data
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
  teacher: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'teacher-saved'])

const form = reactive({
  full_name: '',
  email: '',
  nip: '',
  subject_taught_id: '',
  password: '',
})

const subjects = ref([])

const fetchSubjects = async () => {
  try {
    const response = await axios.get('/api/admin/subjects')
    subjects.value = response.data
  } catch (error) {
    console.error('Gagal mengambil data mata pelajaran:', error)
  }
}

const saveTeacher = async () => {
  try {
    if (props.teacher) {
      await axios.put(`/api/admin/teachers/${props.teacher.id}`, form)
    } else {
      await axios.post('/api/admin/teachers', form)
    }
    emit('teacher-saved')
    emit('close')
  } catch (error) {
    console.error('Gagal menyimpan data guru:', error)
    alert('Gagal menyimpan data. Pastikan semua field terisi dengan benar.')
  }
}

watch(() => props.teacher, (newTeacher) => {
  if (newTeacher) {
    form.full_name = newTeacher.user ? newTeacher.user.full_name : ''
    form.email = newTeacher.user ? newTeacher.user.email : ''
    form.nip = newTeacher.nip || ''
    form.subject_taught_id = newTeacher.subject_taught_id || ''
    form.password = ''
  } else {
    form.full_name = ''
    form.email = ''
    form.nip = ''
    form.subject_taught_id = ''
    form.password = ''
  }
}, { immediate: true })

onMounted(() => {
  fetchSubjects()
})
</script>
