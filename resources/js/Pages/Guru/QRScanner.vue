<template>
  <AppLayout :role="userRole">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Scanner Kamera Presensi QR Code</h1>
        <p class="text-xs text-slate-500 mt-1">Arahkan Kode QR Kartu Pelajar ke kamera untuk mencatat kehadiran siswa secara instan.</p>
      </div>

      <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-xs font-bold text-slate-700">Kamera Siap Digunakan</span>
      </div>
    </div>

    <!-- Scanner & Manual Input Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      
      <!-- Webcam Scanner Card -->
      <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span>📷 Kamera Pemindai QR Code</span>
        </h2>

        <!-- Camera Scanner Element -->
        <div class="bg-slate-900 rounded-2xl overflow-hidden min-h-[300px] border border-slate-800 relative flex items-center justify-center">
          <div id="reader" class="w-full text-white"></div>
        </div>

        <p class="text-[11px] text-slate-400 mt-3 text-center">
          Posisikan Kode QR di dalam kotak pemindai kamera untuk hasil absensi tercepat.
        </p>
      </div>

      <!-- Manual Input / Scanner Barcode USB Card -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900 mb-4">Input Manual / Barcode USB</h2>
          <form @submit.prevent="processManualScan" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Induk Siswa (NIS)</label>
              <input 
                type="text" 
                v-model="manualNis" 
                placeholder="Scan / Ketik NIS (misal: 2025001)" 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                required 
              />
            </div>

            <button 
              type="submit" 
              :disabled="isProcessing"
              class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 active:scale-95"
            >
              <span v-if="isProcessing" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span>Proses Presensi NIS</span>
            </button>
          </form>
        </div>

        <!-- Latest Scan Result Toast -->
        <div v-if="latestStudent" class="mt-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 animate-fade-in">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-extrabold flex items-center justify-center text-lg">
              ✓
            </div>
            <div>
              <p class="text-[10px] uppercase font-bold text-emerald-600">Presensi Berhasil</p>
              <h4 class="font-extrabold text-xs text-emerald-950">{{ latestStudent.name }}</h4>
              <p class="text-[11px] text-emerald-700 font-mono">NIS: {{ latestStudent.nis }} • {{ latestStudent.class_name }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Today's Attendance Log Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-900">Riwayat Presensi QR Hari Ini</h3>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
          Total: {{ scanLog.length }} Siswa Hadir
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="px-6 py-3.5">Waktu Scan</th>
              <th class="px-6 py-3.5">NIS</th>
              <th class="px-6 py-3.5">Nama Siswa</th>
              <th class="px-6 py-3.5">Kelas</th>
              <th class="px-6 py-3.5 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr v-for="(item, idx) in scanLog" :key="idx" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4 font-mono text-slate-500">{{ item.time }}</td>
              <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ item.nis }}</td>
              <td class="px-6 py-4 font-bold text-slate-900">{{ item.name }}</td>
              <td class="px-6 py-4 text-slate-600">{{ item.class_name }}</td>
              <td class="px-6 py-4 text-center">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  Hadir
                </span>
              </td>
            </tr>

            <tr v-if="scanLog.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                Belum ada siswa yang melakukan presensi QR hari ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { Html5QrcodeScanner } from 'html5-qrcode'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role || 'guru')

const manualNis = ref('')
const isProcessing = ref(false)
const latestStudent = ref(null)
const scanLog = ref([])

let html5QrcodeScanner = null

// Web Audio Beep Sound Effect
const playBeep = () => {
  try {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)()
    const osc = audioCtx.createOscillator()
    const gain = audioCtx.createGain()
    osc.type = 'sine'
    osc.frequency.value = 880 // A5 note
    gain.gain.value = 0.1
    osc.connect(gain)
    gain.connect(audioCtx.destination)
    osc.start()
    osc.stop(audioCtx.currentTime + 0.15)
  } catch (e) {
    // Audio context fallback
  }
}

const sendScanRequest = async (nis) => {
  if (isProcessing.value || !nis) return
  isProcessing.value = true

  try {
    const res = await axios.post('/api/attendances/scan-qr', { nis: nis })
    latestStudent.value = res.data.student
    playBeep()

    // Tambahkan ke log tampilan teratas
    scanLog.value.unshift({
      name: res.data.student.name,
      nis: res.data.student.nis,
      class_name: res.data.student.class_name,
      time: res.data.time,
    })

    manualNis.value = ''
  } catch (err) {
    console.error('Gagal presensi QR:', err)
    alert(err.response?.data?.message || 'Gagal memproses presensi QR Code.')
  } finally {
    isProcessing.value = false
  }
}

const processManualScan = () => {
  sendScanRequest(manualNis.value)
}

const onScanSuccess = (decodedText) => {
  sendScanRequest(decodedText)
}

onMounted(() => {
  try {
    html5QrcodeScanner = new Html5QrcodeScanner(
      'reader',
      { fps: 10, qrbox: { width: 250, height: 250 } },
      /* verbose= */ false
    )
    html5QrcodeScanner.render(onScanSuccess, (error) => {
      // Ignore frame parsing errors
    })
  } catch (e) {
    console.error('Gagal menginisialisasi kamera:', e)
  }
})

onUnmounted(() => {
  if (html5QrcodeScanner) {
    html5QrcodeScanner.clear().catch(err => console.error(err))
  }
})
</script>
