<template>
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 sm:p-8">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
        <div>
          <h3 class="text-lg font-bold text-slate-900">
            {{ student ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Isi formulir berikut dengan informasi siswa</p>
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
      <form @submit.prevent="saveStudent" class="space-y-4 text-xs font-medium text-slate-700">
        <div>
          <label class="block mb-1 font-semibold text-slate-700">Nama Lengkap</label>
          <input
            type="text"
            v-model="form.full_name"
            placeholder="Contoh: Ahmad Fauzi"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Email Utama</label>
          <input
            type="email"
            v-model="form.email"
            placeholder="ahmad@sekolah.sch.id"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all disabled:opacity-60"
            :readonly="!!student"
            required
          />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block mb-1 font-semibold text-slate-700">NIS</label>
            <input
              type="text"
              v-model="form.nis"
              placeholder="12345678"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
              required
            />
          </div>

          <div>
            <label class="block mb-1 font-semibold text-slate-700">Kelas</label>
            <select
              v-model="form.class_id"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
              required
            >
              <option value="" disabled>Pilih Kelas</option>
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Tanggal Lahir</label>
          <input
            type="date"
            v-model="form.date_of_birth"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Alamat Tempat Tinggal</label>
          <textarea
            v-model="form.address"
            rows="2"
            placeholder="Alamat lengkap siswa..."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
          ></textarea>
        </div>

        <div>
          <label class="block mb-1 font-semibold text-slate-700">Telepon Orang Tua / Wali</label>
          <input
            type="tel"
            v-model="form.parent_phone_number"
            placeholder="08123456789"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
          />
        </div>

        <div v-if="!student">
          <label class="block mb-1 font-semibold text-slate-700">Password Akun Siswa</label>
          <input
            type="password"
            v-model="form.password"
            placeholder="••••••••"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
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
            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-600/20 transition-all"
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
  student: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'student-saved'])

const form = reactive({
  full_name: '',
  email: '',
  nis: '',
  class_id: '',
  date_of_birth: '',
  address: '',
  parent_phone_number: '',
  password: '',
})

const classes = ref([])

const fetchClasses = async () => {
  try {
    const response = await axios.get('/api/admin/classes')
    classes.value = response.data
  } catch (error) {
    console.error('Gagal mengambil data kelas:', error)
  }
}

const saveStudent = async () => {
  try {
    if (props.student) {
      await axios.put(`/api/admin/students/${props.student.id}`, form)
    } else {
      await axios.post('/api/admin/students', form)
    }
    emit('student-saved')
    emit('close')
  } catch (error) {
    console.error('Gagal menyimpan data siswa:', error)
    alert('Gagal menyimpan data. Pastikan semua field terisi dengan benar.')
  }
}

watch(() => props.student, (newStudent) => {
  if (newStudent) {
    form.full_name = newStudent.user ? newStudent.user.full_name : ''
    form.email = newStudent.user ? newStudent.user.email : ''
    form.nis = newStudent.nis || ''
    form.class_id = newStudent.class_id || ''
    form.date_of_birth = newStudent.date_of_birth || ''
    form.address = newStudent.address || ''
    form.parent_phone_number = newStudent.parent_phone_number || ''
    form.password = ''
  } else {
    form.full_name = ''
    form.email = ''
    form.nis = ''
    form.class_id = ''
    form.date_of_birth = ''
    form.address = ''
    form.parent_phone_number = ''
    form.password = ''
  }
}, { immediate: true })

onMounted(() => {
  fetchClasses()
})
</script>