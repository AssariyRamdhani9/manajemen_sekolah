<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 font-sans p-4 sm:p-6 select-none">
    <!-- Top Header Bar -->
    <header class="max-w-5xl mx-auto bg-slate-800/80 backdrop-blur-md rounded-2xl border border-slate-700/80 p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xl">
      <div>
        <h1 class="text-lg font-extrabold text-white tracking-tight">{{ examData.title || 'Ujian Online CBT' }}</h1>
        <p class="text-xs text-indigo-400 font-medium">Mata Pelajaran: {{ examData.subject_name || '-' }}</p>
      </div>

      <!-- Live Countdown Timer -->
      <div class="flex items-center gap-4 bg-slate-950 px-4 py-2 rounded-xl border border-slate-800 shrink-0">
        <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></div>
        <div>
          <p class="text-[10px] uppercase font-semibold text-slate-400">Sisa Waktu</p>
          <p class="text-base font-extrabold font-mono text-emerald-400 tracking-wider">
            {{ formattedTimer }}
          </p>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <div v-if="loading" class="max-w-5xl mx-auto p-12 text-center text-slate-400 text-xs">
      <div class="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
      Memuat lembar ujian...
    </div>

    <div v-else-if="questions.length === 0" class="max-w-5xl mx-auto bg-slate-800 rounded-3xl p-12 text-center text-slate-400 text-xs border border-slate-700">
      Belum ada soal terdaftar untuk ujian ini.
    </div>

    <div v-else class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Question Section -->
      <div class="lg:col-span-3 bg-slate-800/90 rounded-3xl border border-slate-700/80 p-6 sm:p-8 shadow-xl flex flex-col justify-between">
        <div>
          <!-- Question Header -->
          <div class="flex items-center justify-between pb-4 border-b border-slate-700 mb-6">
            <span class="px-3 py-1 rounded-xl bg-indigo-600/30 text-indigo-300 font-bold text-xs border border-indigo-500/30">
              Soal No. {{ currentIndex + 1 }} dari {{ questions.length }}
            </span>
            <span class="text-xs text-slate-400">Pilihan Ganda</span>
          </div>

          <!-- Question Text -->
          <h3 class="text-base font-bold text-white leading-relaxed mb-6">
            {{ currentQuestion.question_text }}
          </h3>

          <!-- Answer Options -->
          <div class="space-y-3">
            <button
              v-for="optKey in ['A', 'B', 'C', 'D']"
              :key="optKey"
              type="button"
              @click="selectAnswer(currentQuestion.id, optKey)"
              class="w-full text-left p-4 rounded-2xl border transition-all flex items-center gap-3 group"
              :class="[
                userAnswers[currentQuestion.id] === optKey
                  ? 'bg-indigo-600/30 border-indigo-500 text-white shadow-lg shadow-indigo-600/20 font-bold'
                  : 'bg-slate-900/60 border-slate-700/80 text-slate-300 hover:bg-slate-700/50 hover:border-slate-600'
              ]"
            >
              <span 
                class="w-7 h-7 rounded-xl font-bold flex items-center justify-center text-xs shrink-0 transition-colors"
                :class="[
                  userAnswers[currentQuestion.id] === optKey
                    ? 'bg-indigo-600 text-white'
                    : 'bg-slate-800 text-slate-400 group-hover:text-white'
                ]"
              >
                {{ optKey }}
              </span>
              <span class="text-xs leading-relaxed">{{ currentQuestion['option_' + optKey.toLowerCase()] }}</span>
            </button>
          </div>
        </div>

        <!-- Pagination Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-slate-700 mt-8">
          <button 
            @click="prevQuestion" 
            :disabled="currentIndex === 0"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 disabled:opacity-40 text-xs font-bold rounded-xl transition-all"
          >
            ← Soal Sebelumnya
          </button>

          <button 
            v-if="currentIndex < questions.length - 1"
            @click="nextQuestion" 
            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/20"
          >
            Soal Selanjutnya →
          </button>

          <button 
            v-else
            @click="confirmSubmit" 
            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-600/20"
          >
            ✓ Selesai & Kumpulkan
          </button>
        </div>
      </div>

      <!-- Question Grid Navigator -->
      <div class="bg-slate-800/90 rounded-3xl border border-slate-700/80 p-6 shadow-xl h-fit">
        <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4">Navigasi Soal</h4>
        <div class="grid grid-cols-5 gap-2">
          <button
            v-for="(q, idx) in questions"
            :key="q.id"
            @click="currentIndex = idx"
            class="w-full h-9 rounded-xl font-mono text-xs font-bold transition-all border flex items-center justify-center"
            :class="[
              currentIndex === idx
                ? 'ring-2 ring-indigo-400 border-indigo-400'
                : '',
              userAnswers[q.id]
                ? 'bg-emerald-600/30 border-emerald-500 text-emerald-300'
                : 'bg-slate-900/60 border-slate-700 text-slate-400 hover:border-slate-500'
            ]"
          >
            {{ idx + 1 }}
          </button>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-700 space-y-2 text-[11px] text-slate-400">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-md bg-emerald-600/30 border border-emerald-500"></span>
            <span>Sudah Dijawab</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-md bg-slate-900 border border-slate-700"></span>
            <span>Belum Dijawab</span>
          </div>
        </div>

        <button 
          @click="confirmSubmit" 
          class="w-full mt-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all"
        >
          Kumpulkan Jawaban
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  examId: [String, Number]
})

const examData = ref({})
const questions = ref([])
const currentIndex = ref(0)
const userAnswers = reactive({})
const loading = ref(true)

const totalSecondsLeft = ref(3600)
let timerInterval = null

const currentQuestion = computed(() => questions.value[currentIndex.value] || {})

const formattedTimer = computed(() => {
  const minutes = Math.floor(totalSecondsLeft.value / 60)
  const seconds = totalSecondsLeft.value % 60
  return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
})

const selectAnswer = (questionId, optionKey) => {
  userAnswers[questionId] = optionKey
}

const prevQuestion = () => {
  if (currentIndex.value > 0) currentIndex.value--
}

const nextQuestion = () => {
  if (currentIndex.value < questions.value.length - 1) currentIndex.value++
}

const fetchExamDetails = async () => {
  try {
    const res = await axios.get(`/api/exams/${props.examId}`)
    examData.value = res.data
    questions.value = res.data.questions || []
    totalSecondsLeft.value = (res.data.duration_minutes || 60) * 60

    // Mulai sesi pengerjaan di DB
    await axios.post(`/api/exams/${props.examId}/start`)

    startTimer()
  } catch (err) {
    console.error('Gagal memuat ujian:', err)
  } finally {
    loading.value = false
  }
}

const startTimer = () => {
  timerInterval = setInterval(() => {
    if (totalSecondsLeft.value > 0) {
      totalSecondsLeft.value--
    } else {
      clearInterval(timerInterval)
      alert('Waktu pengerjaan telah habis! Jawaban Anda akan otomatis dikumpulkan.')
      submitExam()
    }
  }, 1000)
}

const confirmSubmit = () => {
  const answeredCount = Object.keys(userAnswers).length
  const unAnsweredCount = questions.value.length - answeredCount

  let msg = 'Apakah Anda yakin ingin menyelesaikan dan mengumpulkan ujian ini?'
  if (unAnsweredCount > 0) {
    msg = `Masih ada ${unAnsweredCount} soal yang belum dijawab. Yakin ingin mengumpulkan?`
  }

  if (confirm(msg)) {
    submitExam()
  }
}

const submitExam = async () => {
  clearInterval(timerInterval)
  try {
    const res = await axios.post(`/api/exams/${props.examId}/submit`, {
      answers: userAnswers
    })
    alert(`Ujian berhasil dikumpulkan! Nilai Anda: ${res.data.score}`)
    router.visit('/student/exams')
  } catch (err) {
    console.error('Gagal mengumpulkan ujian:', err)
    alert('Gagal mengumpulkan ujian. Silakan periksa koneksi internet.')
  }
}

// Anti-cheat tab change detection
const handleVisibilityChange = () => {
  if (document.hidden) {
    console.warn('Peringatan: Siswa berpindah tab halaman!')
  }
}

onMounted(() => {
  fetchExamDetails()
  document.addEventListener('visibilitychange', handleVisibilityChange)
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
  document.removeEventListener('visibilitychange', handleVisibilityChange)
})
</script>
