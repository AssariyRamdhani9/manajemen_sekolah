<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Hasil Ujian CBT Siswa</h1>
        <p class="text-xs text-slate-500 mt-1">{{ examData.exam?.title || 'Loading...' }} • {{ examData.exam?.class_name || '' }} ({{ examData.exam?.subject_name || '' }})</p>
      </div>

      <Link
        href="/teacher/exams"
        class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all shadow-sm w-fit"
      >
        ← Kembali ke Daftar Ujian
      </Link>
    </div>

    <!-- Results Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">NIS & Nama Siswa</th>
              <th class="px-6 py-3.5 text-center">Waktu Mulai</th>
              <th class="px-6 py-3.5 text-center">Waktu Selesai</th>
              <th class="px-6 py-3.5 text-center">Status</th>
              <th class="px-6 py-3.5 text-center">Nilai Ujian</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="st in examData.results" :key="st.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ st.student_name }}</p>
                <p class="text-[11px] text-slate-400 font-mono">NIS: {{ st.nis }}</p>
              </td>
              <td class="px-6 py-4 text-center font-mono text-slate-500">
                {{ formatDate(st.started_at) }}
              </td>
              <td class="px-6 py-4 text-center font-mono text-slate-500">
                {{ formatDate(st.finished_at) }}
              </td>
              <td class="px-6 py-4 text-center">
                <span 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold border"
                  :class="st.status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'"
                >
                  {{ st.status === 'completed' ? 'Selesai' : 'Sedang Mengerjakan' }}
                </span>
              </td>
              <td class="px-6 py-4 text-center font-mono font-extrabold text-base text-indigo-600">
                {{ st.score }}
              </td>
            </tr>

            <tr v-if="!examData.results || examData.results.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                Belum ada siswa yang mengerjakan ujian ini.
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
import { Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  examId: [String, Number]
})

const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role || 'guru')
const examData = ref({})

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  try {
    return new Date(dateStr).toLocaleString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      day: 'numeric',
      month: 'short'
    })
  } catch (e) {
    return dateStr
  }
}

const fetchResults = async () => {
  try {
    const res = await axios.get(`/api/exams/${props.examId}/results`)
    examData.value = res.data
  } catch (err) {
    console.error('Gagal mengambil hasil ujian:', err)
  }
}

onMounted(() => {
  fetchResults()
})
</script>
