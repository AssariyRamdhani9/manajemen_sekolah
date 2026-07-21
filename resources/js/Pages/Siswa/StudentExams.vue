<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ujian & Kuis Saya (CBT)</h1>
        <p class="text-xs text-slate-500 mt-1">Daftar paket ujian aktif yang diperuntukkan bagi kelas Anda.</p>
      </div>
    </div>

    <!-- Exam Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="exam in exams" 
        :key="exam.id" 
        class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
      >
        <div>
          <div class="flex items-center justify-between gap-2 mb-3">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
              Mapel: {{ exam.subject_name }}
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
          <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ exam.description || 'Petunjuk pengerjaan terdaftar.' }}</p>

          <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
            <span>Jumlah Soal: <strong class="text-indigo-600 font-mono">{{ exam.total_questions }} Soal</strong></span>
            
            <span 
              v-if="exam.attempt" 
              class="font-bold font-mono px-2.5 py-0.5 rounded-full text-[11px]"
              :class="exam.attempt.status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'"
            >
              {{ exam.attempt.status === 'completed' ? `Nilai: ${exam.attempt.score}` : 'Sedang Dikerjakan' }}
            </span>
            <span v-else class="text-slate-400">Belum Dikerjakan</span>
          </div>
        </div>

        <div class="mt-6 pt-3 border-t border-slate-100">
          <Link 
            v-if="!exam.attempt || exam.attempt.status === 'in_progress'"
            :href="`/student/exams/${exam.id}/take`"
            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2"
          >
            <span>{{ exam.attempt ? 'Lanjutkan Ujian' : 'Mulai Kerjakan Ujian' }}</span>
            <span>→</span>
          </Link>

          <div 
            v-else 
            class="w-full py-2 bg-slate-100 text-slate-500 rounded-xl text-xs font-bold text-center border border-slate-200"
          >
            Ujian Selesai Dikumpulkan
          </div>
        </div>
      </div>

      <div v-if="exams.length === 0" class="col-span-full bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-400 text-xs">
        Belum ada ujian aktif yang diperuntukkan bagi kelas Anda saat ini.
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role || 'siswa')
const exams = ref([])

const fetchExams = async () => {
  try {
    const res = await axios.get('/api/exams')
    exams.value = res.data
  } catch (err) {
    console.error('Gagal memuat ujian siswa:', err)
  }
}

onMounted(() => {
  fetchExams()
})
</script>
