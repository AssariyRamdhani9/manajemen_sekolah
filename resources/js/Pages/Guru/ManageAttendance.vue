<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Absensi Harian Siswa</h1>
        <p class="text-xs text-slate-500 mt-1">Pilih kelas dan tanggal untuk mengisi atau memperbarui kehadiran siswa.</p>
      </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Kelas</label>
          <select 
            v-model="selectedClassId" 
            @change="fetchAttendance"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium"
          >
            <option value="" disabled>-- Pilih Kelas --</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Absensi</label>
          <input 
            type="date" 
            v-model="selectedDate" 
            @change="fetchAttendance"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium"
          />
        </div>

        <div>
          <button 
            @click="submitAttendance" 
            :disabled="!selectedClassId || isLoading || students.length === 0"
            class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 active:scale-95"
          >
            <span v-if="isLoading" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span>Simpan Data Absensi</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Attendance Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div v-if="!selectedClassId" class="p-12 text-center text-slate-400 text-xs">
        Silakan pilih kelas terlebih dahulu untuk menampilkan daftar siswa.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">NIS</th>
              <th class="px-6 py-3.5">Nama Siswa</th>
              <th class="px-6 py-3.5 text-center">Status Kehadiran</th>
              <th class="px-6 py-3.5">Catatan (Keterangan)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="st in students" :key="st.student_id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4 font-mono text-slate-500">{{ st.nis }}</td>
              <td class="px-6 py-4 font-bold text-slate-900">{{ st.name }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-1 bg-slate-100/80 p-1 rounded-2xl w-fit mx-auto border border-slate-200/60">
                  <button
                    type="button"
                    v-for="status in ['Hadir', 'Sakit', 'Izin', 'Alfa']"
                    :key="status"
                    @click="st.status = status"
                    class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition-all"
                    :class="[
                      st.status === status
                        ? getStatusBadgeClass(status)
                        : 'text-slate-500 hover:text-slate-800'
                    ]"
                  >
                    {{ status }}
                  </button>
                </div>
              </td>
              <td class="px-6 py-4">
                <input 
                  type="text" 
                  v-model="st.notes" 
                  placeholder="Opsional (misal: Demam)" 
                  class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                />
              </td>
            </tr>

            <tr v-if="students.length === 0">
              <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-xs">
                Tidak ada data siswa ditemukan di kelas ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role || 'guru')

const classes = ref([])
const selectedClassId = ref('')
const selectedDate = ref(new Date().toISOString().substring(0, 10))
const students = ref([])
const isLoading = ref(false)

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'Hadir': return 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30'
    case 'Sakit': return 'bg-amber-500 text-white shadow-sm shadow-amber-500/30'
    case 'Izin': return 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30'
    case 'Alfa': return 'bg-rose-600 text-white shadow-sm shadow-rose-600/30'
    default: return 'bg-slate-200 text-slate-700'
  }
}

const fetchClasses = async () => {
  try {
    const res = await axios.get('/api/guru/classes')
    classes.value = res.data
    if (classes.value.length > 0) {
      selectedClassId.value = classes.value[0].id
      fetchAttendance()
    }
  } catch (err) {
    console.error('Gagal memuat daftar kelas:', err)
  }
}

const fetchAttendance = async () => {
  if (!selectedClassId.value || !selectedDate.value) return

  isLoading.value = true
  try {
    const res = await axios.get(`/api/attendances?class_id=${selectedClassId.value}&date=${selectedDate.value}`)
    students.value = res.data.students || []
  } catch (err) {
    console.error('Gagal mengambil data absensi:', err)
  } finally {
    isLoading.value = false
  }
}

const submitAttendance = async () => {
  if (!selectedClassId.value || !selectedDate.value) return

  isLoading.value = true
  try {
    await axios.post('/api/attendances', {
      class_id: selectedClassId.value,
      date: selectedDate.value,
      attendances: students.value,
    })
    alert('Data absensi berhasil disimpan!')
  } catch (err) {
    console.error('Gagal menyimpan absensi:', err)
    alert('Gagal menyimpan absensi. Silakan coba lagi.')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchClasses()
})
</script>
