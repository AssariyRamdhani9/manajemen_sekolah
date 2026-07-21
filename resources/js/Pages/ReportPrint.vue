<template>
  <div class="min-h-screen bg-slate-100 p-4 sm:p-8 font-sans print:p-0 print:bg-white text-slate-900">
    <!-- Action Bar for Non-Print View -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between print:hidden">
      <button 
        @click="backToDashboard" 
        class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all shadow-sm"
      >
        ← Kembali ke Dashboard
      </button>
      <button 
        @click="triggerPrint" 
        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-indigo-600/20 flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        <span>Cetak Sekarang (PDF)</span>
      </button>
    </div>

    <!-- Official Report Card Sheet (A4 Concept) -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 shadow-xl print:shadow-none print:p-0 border border-slate-200 print:border-none rounded-2xl print:rounded-none">
      
      <!-- School Header (KOP Surat) -->
      <div class="flex items-center gap-6 pb-6 mb-6 border-b-4 border-double border-slate-900">
        <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-extrabold text-2xl shadow-md">
          S
        </div>
        <div class="flex-1">
          <h1 class="text-xl font-black uppercase tracking-wider text-slate-900">SMA NEGERI SEKOLAHKU</h1>
          <p class="text-xs font-medium text-slate-600 mt-0.5">Jalan Pendidikan No. 123, Kota Akademik, Telp: (021) 555-0199</p>
          <p class="text-[11px] text-slate-500">Website: www.sekolahku.sch.id • Email: info@sekolahku.sch.id</p>
        </div>
      </div>

      <!-- Document Title -->
      <div class="text-center mb-8">
        <h2 class="text-base font-extrabold uppercase tracking-widest text-slate-900 underline underline-offset-4">LAPORAN HASIL BELAJAR SISWA (RAPOR)</h2>
        <p class="text-xs text-slate-500 mt-1">Tahun Ajaran {{ reportData.academic_year || '2025/2026' }} • Semester {{ reportData.semester || '1' }}</p>
      </div>

      <!-- Student Bio Table -->
      <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-xs font-medium text-slate-700 mb-8 p-4 rounded-xl bg-slate-50 border border-slate-200/80 print:bg-transparent print:p-0 print:border-none">
        <div>
          <span class="text-slate-500 w-28 inline-block">Nama Siswa</span>: <strong class="text-slate-900 uppercase">{{ reportData.student?.name || '-' }}</strong>
        </div>
        <div>
          <span class="text-slate-500 w-28 inline-block">Kelas</span>: <strong class="text-slate-900">{{ reportData.student?.class_name || '-' }}</strong>
        </div>
        <div>
          <span class="text-slate-500 w-28 inline-block">NIS / NISN</span>: <strong class="text-slate-900 font-mono">{{ reportData.student?.nis || '-' }}</strong>
        </div>
        <div>
          <span class="text-slate-500 w-28 inline-block">Rata-Rata Nilai</span>: <strong class="text-emerald-700 font-mono text-sm">{{ reportData.average_score || 0 }}</strong>
        </div>
      </div>

      <!-- Grades Table -->
      <div class="mb-8">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-3">A. CAPAIAN HASIL BELAJAR AKADEMIK</h3>
        <table class="w-full text-left border-collapse border border-slate-900 text-xs">
          <thead>
            <tr class="bg-slate-100 text-slate-900 font-bold border-b border-slate-900 text-center uppercase tracking-wider text-[11px]">
              <th class="border border-slate-900 px-3 py-2 w-10">No</th>
              <th class="border border-slate-900 px-3 py-2 text-left">Mata Pelajaran</th>
              <th class="border border-slate-900 px-2 py-2 w-16">Tugas</th>
              <th class="border border-slate-900 px-2 py-2 w-16">UTS</th>
              <th class="border border-slate-900 px-2 py-2 w-16">UAS</th>
              <th class="border border-slate-900 px-2 py-2 w-20">Nilai Akhir</th>
              <th class="border border-slate-900 px-2 py-2 w-16">Predikat</th>
              <th class="border border-slate-900 px-3 py-2 text-left">Deskripsi Capaian</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(g, idx) in reportData.grades" :key="g.id" class="border-b border-slate-800">
              <td class="border border-slate-900 px-3 py-2 text-center font-mono">{{ idx + 1 }}</td>
              <td class="border border-slate-900 px-3 py-2 font-bold text-slate-900">{{ g.subject_name }}</td>
              <td class="border border-slate-900 px-2 py-2 text-center font-mono">{{ g.assignment_score }}</td>
              <td class="border border-slate-900 px-2 py-2 text-center font-mono">{{ g.uts_score }}</td>
              <td class="border border-slate-900 px-2 py-2 text-center font-mono">{{ g.uas_score }}</td>
              <td class="border border-slate-900 px-2 py-2 text-center font-mono font-extrabold text-slate-900">{{ g.final_score }}</td>
              <td class="border border-slate-900 px-2 py-2 text-center font-bold">{{ g.letter_grade }}</td>
              <td class="border border-slate-900 px-3 py-2 text-slate-700 leading-tight">{{ g.teacher_notes }}</td>
            </tr>

            <tr v-if="!reportData.grades || reportData.grades.length === 0">
              <td colspan="8" class="border border-slate-900 px-3 py-6 text-center text-slate-400 italic">
                Belum ada data nilai yang terdaftar untuk semester ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Attendance Table -->
      <div class="mb-12">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-3">B. REKAPITULASI KEHADIRAN SISWA</h3>
        <table class="w-72 text-left border-collapse border border-slate-900 text-xs">
          <thead>
            <tr class="bg-slate-100 border-b border-slate-900 font-bold uppercase text-[10px]">
              <th class="border border-slate-900 px-3 py-2">Keterangan</th>
              <th class="border border-slate-900 px-3 py-2 text-center">Jumlah Hari</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border border-slate-900 px-3 py-1.5 font-medium">Hadir</td>
              <td class="border border-slate-900 px-3 py-1.5 text-center font-mono font-bold">{{ reportData.attendance?.hadir || 0 }} hari</td>
            </tr>
            <tr>
              <td class="border border-slate-900 px-3 py-1.5 font-medium">Sakit</td>
              <td class="border border-slate-900 px-3 py-1.5 text-center font-mono font-bold">{{ reportData.attendance?.sakit || 0 }} hari</td>
            </tr>
            <tr>
              <td class="border border-slate-900 px-3 py-1.5 font-medium">Izin</td>
              <td class="border border-slate-900 px-3 py-1.5 text-center font-mono font-bold">{{ reportData.attendance?.izin || 0 }} hari</td>
            </tr>
            <tr>
              <td class="border border-slate-900 px-3 py-1.5 font-medium">Tanpa Keterangan (Alfa)</td>
              <td class="border border-slate-900 px-3 py-1.5 text-center font-mono font-bold">{{ reportData.attendance?.alfa || 0 }} hari</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Signature Block -->
      <div class="grid grid-cols-3 text-center text-xs font-medium text-slate-800 pt-6 mt-8 border-t border-slate-300">
        <div>
          <p class="mb-16">Mengetahui,<br><strong>Orang Tua / Wali Siswa</strong></p>
          <p class="font-bold underline decoration-slate-400 decoration-1">( ........................................ )</p>
        </div>

        <div>
          <p class="mb-16">Kota Akademik, {{ currentDate }}<br><strong>Wali Kelas</strong></p>
          <p class="font-bold underline decoration-slate-400 decoration-1">( ........................................ )</p>
        </div>

        <div>
          <p class="mb-16">Mengetahui,<br><strong>Kepala Sekolah</strong></p>
          <p class="font-bold underline decoration-slate-400 decoration-1">Dr. Ari, M.Pd</p>
          <p class="text-[10px] text-slate-500">NIP. 19800101 200501 1 001</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  studentId: [String, Number]
})

const reportData = ref({})
const currentDate = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })

const fetchReport = async () => {
  try {
    const url = props.studentId ? `/api/student/report?student_id=${props.studentId}` : '/api/student/report'
    const res = await axios.get(url)
    reportData.value = res.data

    // Memicu dialog cetak otomatis saat data selesai dimuat
    setTimeout(() => {
      window.print()
    }, 500)
  } catch (err) {
    console.error('Gagal mengambil data cetak rapor:', err)
  }
}

const triggerPrint = () => {
  window.print()
}

const backToDashboard = () => {
  window.history.back()
}

onMounted(() => {
  fetchReport()
})
</script>
