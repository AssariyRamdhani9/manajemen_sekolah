<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kartu Pelajar Digital (KTS)</h1>
        <p class="text-xs text-slate-500 mt-1">Kartu identitas resmi siswa yang dilengkapi Kode QR unik untuk presensi otomatis.</p>
      </div>

      <button 
        @click="triggerPrint"
        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2 active:scale-95 w-fit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        <span>Cetak Kartu Tanda Siswa</span>
      </button>
    </div>

    <!-- Student Card Display (Standard ID Card Aspect Ratio 85.6mm x 54mm) -->
    <div class="max-w-md mx-auto print:max-w-none print:m-0 print:p-0">
      <div class="bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-7 text-white shadow-2xl border border-indigo-500/30 relative overflow-hidden print:shadow-none print:border-slate-900">
        
        <!-- Background Decorative Orbs -->
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-indigo-500/20 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-purple-500/20 rounded-full blur-2xl"></div>

        <!-- School Header -->
        <div class="flex items-center justify-between pb-4 border-b border-indigo-500/30 relative z-10">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-black text-lg shadow-lg">
              S
            </div>
            <div>
              <h3 class="font-extrabold text-sm uppercase tracking-tight text-white">SMA NEGERI SEKOLAHKU</h3>
              <p class="text-[10px] text-indigo-300 font-semibold uppercase tracking-widest">KARTU TANDA SISWA (KTS)</p>
            </div>
          </div>
          <span class="text-[9px] font-mono px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold uppercase">AKTIF</span>
        </div>

        <!-- Student Details & QR Code Grid -->
        <div class="mt-6 grid grid-cols-3 gap-4 items-center relative z-10">
          <!-- Student Info -->
          <div class="col-span-2 space-y-3">
            <div>
              <p class="text-[10px] uppercase font-semibold text-slate-400">Nama Lengkap Siswa</p>
              <p class="text-sm font-extrabold text-white leading-snug uppercase tracking-tight">{{ studentInfo.name }}</p>
            </div>

            <div>
              <p class="text-[10px] uppercase font-semibold text-slate-400">Nomor Induk Siswa (NIS)</p>
              <p class="text-sm font-bold font-mono text-indigo-300">{{ studentInfo.nis }}</p>
            </div>

            <div>
              <p class="text-[10px] uppercase font-semibold text-slate-400">Kelas / Rombel</p>
              <p class="text-xs font-bold text-white">{{ studentInfo.class_name }}</p>
            </div>
          </div>

          <!-- Dynamic QR Code Card -->
          <div class="bg-white p-2.5 rounded-2xl border border-indigo-400/30 shadow-lg text-center flex flex-col items-center justify-center">
            <img 
              :src="qrCodeUrl" 
              alt="Kode QR Presensi Siswa" 
              class="w-24 h-24 object-contain rounded-lg"
            />
            <p class="text-[9px] font-mono text-slate-900 font-bold mt-1 tracking-wider">SCAN PRESENSI</p>
          </div>
        </div>

        <!-- Footer Card -->
        <div class="mt-6 pt-3 border-t border-indigo-500/20 flex items-center justify-between text-[10px] text-indigo-300 font-medium relative z-10">
          <span>Tahun Ajaran 2025/2026</span>
          <span>Bawa kartu ini untuk presensi kamera</span>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role || 'siswa')

const studentInfo = ref({
  name: page.props.auth?.user?.full_name || 'Siswa',
  nis: '2025001',
  class_name: 'XI IPA 2',
})

const qrCodeUrl = computed(() => {
  const data = encodeURIComponent(studentInfo.value.nis || '2025001')
  return `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${data}&margin=0`
})

const fetchStudentDetails = async () => {
  try {
    const res = await axios.get('/api/user')
    if (res.data.student) {
      studentInfo.value.name = res.data.full_name
      studentInfo.value.nis = res.data.student.nis
      studentInfo.value.class_name = res.data.student.class?.name || res.data.student.school_class?.name || '-'
    }
  } catch (err) {
    console.error('Gagal mengambil detail siswa:', err)
  }
}

const triggerPrint = () => {
  window.print()
}

onMounted(() => {
  fetchStudentDetails()
})
</script>
