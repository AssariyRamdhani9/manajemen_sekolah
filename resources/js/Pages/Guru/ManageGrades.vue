<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Olah Nilai & Rapor Digital</h1>
        <p class="text-xs text-slate-500 mt-1">Input nilai tugas, UTS, UAS, serta catatan perkembangan akademik siswa.</p>
      </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Kelas</label>
          <select 
            v-model="selectedClassId" 
            @change="fetchGrades"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium"
          >
            <option value="" disabled>-- Pilih Kelas --</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Mata Pelajaran</label>
          <select 
            v-model="selectedSubjectId" 
            @change="fetchGrades"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium"
          >
            <option value="" disabled>-- Pilih Mapel --</option>
            <option v-for="subj in subjects" :key="subj.id" :value="subj.id">{{ subj.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Semester</label>
          <select 
            v-model="selectedSemester" 
            @change="fetchGrades"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium"
          >
            <option value="1">Semester 1 (Ganjil)</option>
            <option value="2">Semester 2 (Genap)</option>
          </select>
        </div>

        <div>
          <button 
            @click="submitGrades" 
            :disabled="!selectedClassId || !selectedSubjectId || isLoading || grades.length === 0"
            class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center gap-2 active:scale-95"
          >
            <span v-if="isLoading" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span>Simpan Semua Nilai</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Grade Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div v-if="!selectedClassId || !selectedSubjectId" class="p-12 text-center text-slate-400 text-xs">
        Silakan pilih Kelas dan Mata Pelajaran untuk menginputkan nilai siswa.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">NIS & Nama Siswa</th>
              <th class="px-4 py-3.5 text-center w-28">Tugas (30%)</th>
              <th class="px-4 py-3.5 text-center w-28">UTS (30%)</th>
              <th class="px-4 py-3.5 text-center w-28">UAS (40%)</th>
              <th class="px-4 py-3.5 text-center w-28">Nilai Akhir</th>
              <th class="px-4 py-3.5 text-center w-20">Predikat</th>
              <th class="px-6 py-3.5">Catatan Kompetensi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="st in grades" :key="st.student_id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ st.name }}</p>
                <p class="text-[11px] text-slate-400 font-mono">NIS: {{ st.nis }}</p>
              </td>
              <td class="px-4 py-4 text-center">
                <input 
                  type="number" 
                  min="0" 
                  max="100" 
                  v-model.number="st.assignment_score" 
                  @input="recalculate(st)"
                  class="w-20 px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-center text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
              </td>
              <td class="px-4 py-4 text-center">
                <input 
                  type="number" 
                  min="0" 
                  max="100" 
                  v-model.number="st.uts_score" 
                  @input="recalculate(st)"
                  class="w-20 px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-center text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
              </td>
              <td class="px-4 py-4 text-center">
                <input 
                  type="number" 
                  min="0" 
                  max="100" 
                  v-model.number="st.uas_score" 
                  @input="recalculate(st)"
                  class="w-20 px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-center text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
              </td>
              <td class="px-4 py-4 text-center font-mono font-extrabold text-sm text-indigo-600">
                {{ st.final_score }}
              </td>
              <td class="px-4 py-4 text-center">
                <span 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold border"
                  :class="getLetterBadgeClass(st.letter_grade)"
                >
                  {{ st.letter_grade }}
                </span>
              </td>
              <td class="px-6 py-4">
                <input 
                  type="text" 
                  v-model="st.teacher_notes" 
                  placeholder="Deskripsi capaian..." 
                  class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
              </td>
            </tr>

            <tr v-if="grades.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
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
const subjects = ref([])
const selectedClassId = ref('')
const selectedSubjectId = ref('')
const selectedSemester = ref('1')
const academicYear = ref('2025/2026')

const grades = ref([])
const isLoading = ref(false)

const getLetterBadgeClass = (letter) => {
  switch (letter) {
    case 'A': return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'B': return 'bg-sky-50 text-sky-700 border-sky-200'
    case 'C': return 'bg-amber-50 text-amber-700 border-amber-200'
    default: return 'bg-rose-50 text-rose-700 border-rose-200'
  }
}

const recalculate = (st) => {
  const ass = Number(st.assignment_score) || 0
  const uts = Number(st.uts_score) || 0
  const uas = Number(st.uas_score) || 0

  const finalScore = Math.round(((ass * 0.30) + (uts * 0.30) + (uas * 0.40)) * 100) / 100
  st.final_score = finalScore

  if (finalScore >= 85) st.letter_grade = 'A'
  else if (finalScore >= 75) st.letter_grade = 'B'
  else if (finalScore >= 65) st.letter_grade = 'C'
  else st.letter_grade = 'D'
}

const fetchInitialData = async () => {
  try {
    const [cRes, sRes] = await Promise.all([
      axios.get('/api/guru/classes'),
      axios.get('/api/guru/subjects')
    ])
    classes.value = cRes.data
    subjects.value = sRes.data

    if (classes.value.length > 0) selectedClassId.value = classes.value[0].id
    if (subjects.value.length > 0) selectedSubjectId.value = subjects.value[0].id

    if (selectedClassId.value && selectedSubjectId.value) {
      fetchGrades()
    }
  } catch (err) {
    console.error('Gagal mengambil data kelas/mapel:', err)
  }
}

const fetchGrades = async () => {
  if (!selectedClassId.value || !selectedSubjectId.value) return

  isLoading.value = true
  try {
    const res = await axios.get(`/api/grades?class_id=${selectedClassId.value}&subject_id=${selectedSubjectId.value}&semester=${selectedSemester.value}&academic_year=${academicYear.value}`)
    grades.value = res.data.grades || []
  } catch (err) {
    console.error('Gagal mengambil data nilai:', err)
  } finally {
    isLoading.value = false
  }
}

const submitGrades = async () => {
  if (!selectedClassId.value || !selectedSubjectId.value) return

  isLoading.value = true
  try {
    await axios.post('/api/grades', {
      class_id: selectedClassId.value,
      subject_id: selectedSubjectId.value,
      semester: selectedSemester.value,
      academic_year: academicYear.value,
      grades: grades.value,
    })
    alert('Data nilai berhasil disimpan!')
  } catch (err) {
    console.error('Gagal menyimpan data nilai:', err)
    alert('Gagal menyimpan nilai. Pastikan format angka sudah benar.')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchInitialData()
})
</script>
