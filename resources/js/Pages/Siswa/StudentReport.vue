<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Rapor Hasil Belajar Digital</h1>
        <p class="text-xs text-slate-500 mt-1">Laporan pencapaian nilai akademik dan rekapitulasi presensi siswa.</p>
      </div>

      <div class="flex items-center gap-3">
        <a
          :href="`/report/print/${reportData.student?.id || ''}`"
          target="_blank"
          class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2 active:scale-95 w-fit"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          <span>Cetak Rapor PDF</span>
        </a>
      </div>
    </div>

    <!-- Student Info Card -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl mb-8 border border-slate-800">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl bg-indigo-600/30 border border-indigo-400/30 flex items-center justify-center text-xl font-extrabold text-indigo-300">
            {{ reportData.student?.name ? reportData.student.name[0] : 'S' }}
          </div>
          <div>
            <h2 class="text-xl font-bold text-white tracking-tight">{{ reportData.student?.name || 'Siswa' }}</h2>
            <p class="text-xs text-indigo-300 mt-0.5">NIS: {{ reportData.student?.nis || '-' }} • Kelas: {{ reportData.student?.class_name || '-' }}</p>
          </div>
        </div>

        <div class="flex items-center gap-6 border-t md:border-t-0 md:border-l border-slate-800 pt-4 md:pt-0 md:pl-6">
          <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400">Rata-Rata Nilai</p>
            <p class="text-2xl font-extrabold text-emerald-400 mt-0.5 font-mono">{{ reportData.average_score || 0 }}</p>
          </div>
          <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400">Semester</p>
            <p class="text-sm font-bold text-white mt-1">Semester {{ reportData.semester || '1' }}</p>
          </div>
          <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400">Tahun Ajaran</p>
            <p class="text-sm font-bold text-white mt-1">{{ reportData.academic_year || '2025/2026' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Attendance Summary Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-slate-400 uppercase">Hadir</p>
        <p class="text-2xl font-extrabold text-emerald-600 font-mono mt-1">{{ reportData.attendance?.hadir || 0 }} <span class="text-xs font-normal text-slate-500">Hari</span></p>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-slate-400 uppercase">Sakit</p>
        <p class="text-2xl font-extrabold text-amber-500 font-mono mt-1">{{ reportData.attendance?.sakit || 0 }} <span class="text-xs font-normal text-slate-500">Hari</span></p>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-slate-400 uppercase">Izin</p>
        <p class="text-2xl font-extrabold text-indigo-600 font-mono mt-1">{{ reportData.attendance?.izin || 0 }} <span class="text-xs font-normal text-slate-500">Hari</span></p>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-slate-400 uppercase">Tanpa Keterangan</p>
        <p class="text-2xl font-extrabold text-rose-600 font-mono mt-1">{{ reportData.attendance?.alfa || 0 }} <span class="text-xs font-normal text-slate-500">Hari</span></p>
      </div>
    </div>

    <!-- Grades List Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-8">
      <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-900">Nilai Hasil Belajar per Mata Pelajaran</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">Mata Pelajaran</th>
              <th class="px-4 py-3.5 text-center">Tugas</th>
              <th class="px-4 py-3.5 text-center">UTS</th>
              <th class="px-4 py-3.5 text-center">UAS</th>
              <th class="px-4 py-3.5 text-center">Nilai Akhir</th>
              <th class="px-4 py-3.5 text-center">Predikat</th>
              <th class="px-6 py-3.5">Capaian Kompetensi / Catatan Guru</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="g in reportData.grades" :key="g.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ g.subject_name }}</p>
                <p class="text-[10px] text-slate-400">Guru: {{ g.teacher_name }}</p>
              </td>
              <td class="px-4 py-4 text-center font-mono text-slate-600">{{ g.assignment_score }}</td>
              <td class="px-4 py-4 text-center font-mono text-slate-600">{{ g.uts_score }}</td>
              <td class="px-4 py-4 text-center font-mono text-slate-600">{{ g.uas_score }}</td>
              <td class="px-4 py-4 text-center font-mono font-extrabold text-sm text-indigo-600">
                {{ g.final_score }}
              </td>
              <td class="px-4 py-4 text-center">
                <span 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold border"
                  :class="getLetterBadgeClass(g.letter_grade)"
                >
                  {{ g.letter_grade }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-600 leading-relaxed italic">
                "{{ g.teacher_notes }}"
              </td>
            </tr>

            <tr v-if="!reportData.grades || reportData.grades.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                Belum ada data nilai yang diinputkan oleh guru untuk semester ini.
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
const userRole = computed(() => page.props.auth?.user?.role || 'siswa')
const reportData = ref({})

const getLetterBadgeClass = (letter) => {
  switch (letter) {
    case 'A': return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'B': return 'bg-sky-50 text-sky-700 border-sky-200'
    case 'C': return 'bg-amber-50 text-amber-700 border-amber-200'
    default: return 'bg-rose-50 text-rose-700 border-rose-200'
  }
}

const fetchReport = async () => {
  try {
    const res = await axios.get('/api/student/report')
    reportData.value = res.data
  } catch (err) {
    console.error('Gagal mengambil data rapor:', err)
  }
}

onMounted(() => {
  fetchReport()
})
</script>
