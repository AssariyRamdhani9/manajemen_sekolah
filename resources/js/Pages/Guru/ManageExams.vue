<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ujian Online & Bank Soal (CBT)</h1>
        <p class="text-xs text-slate-500 mt-1">Buat paket ujian baru, kelola pertanyaan pilihan ganda, dan pantau durasi pengerjaan.</p>
      </div>

      <button
        @click="openExamModal()"
        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2 active:scale-95 w-fit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Buat Ujian Baru</span>
      </button>
    </div>

    <!-- Exam Cards List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="exam in exams" 
        :key="exam.id" 
        class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
      >
        <div>
          <div class="flex items-center justify-between gap-2 mb-3">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
              {{ exam.class_name }} • {{ exam.subject_name }}
            </span>
            <span 
              class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border"
              :class="[
                exam.exam_type === 'UTS' ? 'bg-amber-50 text-amber-700 border-amber-200' :
                (exam.exam_type === 'UAS' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200')
              ]"
            >
              {{ exam.exam_type || 'Kuis' }}
            </span>
          </div>

          <h3 class="text-base font-bold text-slate-900 leading-snug">{{ exam.title }}</h3>
          <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ exam.description || 'Tidak ada deskripsi' }}</p>

          <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
            <span>Jumlah Soal: <strong class="text-indigo-600 font-mono">{{ exam.total_questions }} Soal</strong></span>
            <span>Peserta Mengerjakan: <strong class="text-emerald-600 font-mono">{{ exam.total_attempts }} Siswa</strong></span>
          </div>
        </div>

        <div class="mt-6 flex items-center gap-2 pt-3 border-t border-slate-100">
          <button 
            @click="openQuestionModal(exam)"
            class="flex-1 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition-all border border-indigo-200/60"
          >
            Kelola Soal ({{ exam.total_questions }})
          </button>
          
          <Link 
            :href="`/teacher/exams/${exam.id}/results`"
            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all"
          >
            Hasil
          </Link>

          <button 
            @click="deleteExam(exam.id)"
            class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-all"
            title="Hapus Ujian"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="exams.length === 0" class="col-span-full bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-400 text-xs">
        Belum ada paket ujian CBT yang dibuat saat ini.
      </div>
    </div>

    <!-- Modal Create Exam -->
    <div v-if="showExamModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Buat Ujian Online Baru</h3>
        <form @submit.prevent="saveExam" class="space-y-4 text-xs font-medium text-slate-700">
          <div>
            <label class="block mb-1 font-semibold">Pilih Kelas</label>
            <select v-model="examForm.class_id" class="w-full px-3 py-2 bg-slate-50 border rounded-xl" required>
              <option value="" disabled>Pilih Kelas</option>
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
            </select>
          </div>

          <div>
            <label class="block mb-1 font-semibold">Pilih Mata Pelajaran</label>
            <select v-model="examForm.subject_id" class="w-full px-3 py-2 bg-slate-50 border rounded-xl" required>
              <option value="" disabled>Pilih Mapel</option>
              <option v-for="subj in subjects" :key="subj.id" :value="subj.id">{{ subj.name }}</option>
            </select>
          </div>

          <div>
            <label class="block mb-1 font-semibold">Kategori Ujian</label>
            <select v-model="examForm.exam_type" class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold" required>
              <option value="Kuis">Kuis / Harian</option>
              <option value="UTS">UTS (Ujian Tengah Semester)</option>
              <option value="UAS">UAS (Ujian Akhir Semester)</option>
            </select>
          </div>

          <div>
            <label class="block mb-1 font-semibold">Judul Ujian</label>
            <input type="text" v-model="examForm.title" placeholder="Misal: UTS Matematika Semester 1" class="w-full px-3 py-2 bg-slate-50 border rounded-xl" required />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Durasi Pengerjaan (Menit)</label>
            <input type="number" min="5" max="180" v-model.number="examForm.duration_minutes" class="w-full px-3 py-2 bg-slate-50 border rounded-xl" required />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Keterangan / Petunjuk Ujian</label>
            <textarea v-model="examForm.description" rows="3" placeholder="Petunjuk pengerjaan..." class="w-full px-3 py-2 bg-slate-50 border rounded-xl"></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t">
            <button type="button" @click="showExamModal = false" class="px-4 py-2 bg-slate-100 rounded-xl font-bold">Batal</button>
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl font-bold">Simpan Paket Ujian</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Manage Questions -->
    <div v-if="showQuestionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 border-b mb-6">
          <div>
            <h3 class="text-lg font-bold text-slate-900">Kelola Bank Soal</h3>
            <p class="text-xs text-slate-500">{{ selectedExam?.title }}</p>
          </div>
          <button @click="showQuestionModal = false" class="p-2 text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <!-- Add Question Form -->
        <form @submit.prevent="addQuestion" class="space-y-3 text-xs mb-8 p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
          <h4 class="font-bold text-slate-900">Tambah Pertanyaan Baru</h4>
          <div>
            <label class="block mb-1 font-semibold">Teks Pertanyaan (Soal)</label>
            <textarea v-model="questionForm.question_text" rows="2" placeholder="Tuliskan soal ujian..." class="w-full px-3 py-2 bg-white border rounded-xl" required></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block mb-1 font-semibold text-indigo-700">Pilihan A</label>
              <input type="text" v-model="questionForm.option_a" class="w-full px-3 py-1.5 bg-white border rounded-xl" required />
            </div>
            <div>
              <label class="block mb-1 font-semibold text-indigo-700">Pilihan B</label>
              <input type="text" v-model="questionForm.option_b" class="w-full px-3 py-1.5 bg-white border rounded-xl" required />
            </div>
            <div>
              <label class="block mb-1 font-semibold text-indigo-700">Pilihan C</label>
              <input type="text" v-model="questionForm.option_c" class="w-full px-3 py-1.5 bg-white border rounded-xl" required />
            </div>
            <div>
              <label class="block mb-1 font-semibold text-indigo-700">Pilihan D</label>
              <input type="text" v-model="questionForm.option_d" class="w-full px-3 py-1.5 bg-white border rounded-xl" required />
            </div>
          </div>

          <div>
            <label class="block mb-1 font-semibold text-emerald-700">Kunci Jawaban Benar</label>
            <select v-model="questionForm.correct_option" class="w-full px-3 py-2 bg-white border rounded-xl font-bold" required>
              <option value="A">Pilihan A</option>
              <option value="B">Pilihan B</option>
              <option value="C">Pilihan C</option>
              <option value="D">Pilihan D</option>
            </select>
          </div>

          <button type="submit" class="w-full py-2 bg-indigo-600 text-white font-bold rounded-xl shadow-sm">Tambah Soal Ini</button>
        </form>

        <!-- Question List -->
        <div class="space-y-4">
          <h4 class="font-bold text-slate-900 text-xs">Daftar Soal Terdaftar ({{ currentQuestions.length }})</h4>
          <div v-for="(q, idx) in currentQuestions" :key="q.id" class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2 text-xs">
            <div class="flex justify-between items-start">
              <p class="font-bold text-slate-900">{{ idx + 1 }}. {{ q.question_text }}</p>
              <button @click="deleteQuestion(q.id)" class="text-rose-600 hover:bg-rose-50 p-1 rounded-lg text-[11px] font-bold">Hapus</button>
            </div>
            <div class="grid grid-cols-2 gap-2 text-slate-600 pt-2">
              <span :class="{'font-bold text-emerald-600': q.correct_option === 'A'}">A. {{ q.option_a }}</span>
              <span :class="{'font-bold text-emerald-600': q.correct_option === 'B'}">B. {{ q.option_b }}</span>
              <span :class="{'font-bold text-emerald-600': q.correct_option === 'C'}">C. {{ q.option_c }}</span>
              <span :class="{'font-bold text-emerald-600': q.correct_option === 'D'}">D. {{ q.option_d }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role || 'guru')

const exams = ref([])
const classes = ref([])
const subjects = ref([])

const showExamModal = ref(false)
const showQuestionModal = ref(false)

const selectedExam = ref(null)
const currentQuestions = ref([])

const examForm = reactive({
  class_id: '',
  subject_id: '',
  title: '',
  exam_type: 'Kuis',
  description: '',
  duration_minutes: 60,
})

const questionForm = reactive({
  question_text: '',
  option_a: '',
  option_b: '',
  option_c: '',
  option_d: '',
  correct_option: 'A',
})

const fetchInitialData = async () => {
  try {
    const [eRes, cRes, sRes] = await Promise.all([
      axios.get('/api/exams'),
      axios.get('/api/guru/classes'),
      axios.get('/api/guru/subjects')
    ])
    exams.value = eRes.data
    classes.value = cRes.data
    subjects.value = sRes.data

    if (classes.value.length > 0) examForm.class_id = classes.value[0].id
    if (subjects.value.length > 0) examForm.subject_id = subjects.value[0].id
  } catch (err) {
    console.error('Gagal memuat data ujian:', err)
  }
}

const openExamModal = () => {
  showExamModal.value = true
}

const saveExam = async () => {
  try {
    await axios.post('/api/exams', examForm)
    showExamModal.value = false
    examForm.title = ''
    examForm.description = ''
    fetchInitialData()
  } catch (err) {
    console.error('Gagal membuat ujian:', err)
    alert('Gagal membuat ujian. Pastikan semua bidang terisi.')
  }
}

const deleteExam = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus paket ujian ini?')) {
    try {
      await axios.delete(`/api/exams/${id}`)
      fetchInitialData()
    } catch (err) {
      console.error('Gagal menghapus ujian:', err)
    }
  }
}

const openQuestionModal = async (exam) => {
  selectedExam.value = exam
  try {
    const res = await axios.get(`/api/exams/${exam.id}`)
    currentQuestions.value = res.data.questions || []
    showQuestionModal.value = true
  } catch (err) {
    console.error('Gagal memuat pertanyaan:', err)
  }
}

const addQuestion = async () => {
  if (!selectedExam.value) return
  try {
    await axios.post(`/api/exams/${selectedExam.value.id}/questions`, questionForm)
    questionForm.question_text = ''
    questionForm.option_a = ''
    questionForm.option_b = ''
    questionForm.option_c = ''
    questionForm.option_d = ''
    openQuestionModal(selectedExam.value)
    fetchInitialData()
  } catch (err) {
    console.error('Gagal menambahkan soal:', err)
  }
}

const deleteQuestion = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
    try {
      await axios.delete(`/api/exam-questions/${id}`)
      openQuestionModal(selectedExam.value)
      fetchInitialData()
    } catch (err) {
      console.error('Gagal menghapus soal:', err)
    }
  }
}

onMounted(() => {
  fetchInitialData()
})
</script>
