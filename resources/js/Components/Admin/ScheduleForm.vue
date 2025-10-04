<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-2xl font-semibold leading-tight text-gray-900 mb-4">
          {{ schedule ? 'Edit Jadwal' : 'Tambah Jadwal Baru' }}
        </h3>
        
        <form @submit.prevent="saveSchedule" class="mt-6 space-y-4">
          
          <div class="text-left">
            <label for="class_id" class="block text-sm font-medium text-gray-700">Kelas</label>
            <select
              id="class_id"
              v-model="form.class_id"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required
            >
              <option value="" disabled>Pilih Kelas</option>
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
            </select>
          </div>
          
          <div class="text-left">
            <label for="subject_id" class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
            <select
              id="subject_id"
              v-model="form.subject_id"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required
            >
              <option value="" disabled>Pilih Mata Pelajaran</option>
              <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="text-left">
              <label for="day_of_week" class="block text-sm font-medium text-gray-700">Hari</label>
              <select
                id="day_of_week"
                v-model="form.day_of_week"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                required
              >
                <option value="" disabled>Pilih Hari</option>
                <option>Senin</option>
                <option>Selasa</option>
                <option>Rabu</option>
                <option>Kamis</option>
                <option>Jumat</option>
              </select>
            </div>
            <div class="text-left">
              <label for="start_time" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
              <input
                type="time"
                id="start_time"
                v-model="form.start_time"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                required
              />
            </div>
            <div class="text-left col-span-2">
              <label for="end_time" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
              <input
                type="time"
                id="end_time"
                v-model="form.end_time"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                required
              />
            </div>
          </div>
          
          <div class="flex justify-end gap-x-2 mt-6">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 bg-gray-300 text-gray-800 font-medium rounded-md shadow-sm hover:bg-gray-400"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md shadow-sm hover:bg-blue-700"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  schedule: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'schedule-saved']);

const form = reactive({
  class_id: '',
  subject_id: '',
  day_of_week: '',
  start_time: '',
  end_time: '',
});

const classes = ref([]);
const subjects = ref([]);

const fetchInitialData = async () => {
  try {
    const classesResponse = await axios.get('/api/guru/classes');
    classes.value = classesResponse.data;

    const subjectsResponse = await axios.get('/api/guru/subjects');
    subjects.value = subjectsResponse.data;
  } catch (error) {
    console.error('Gagal mengambil data kelas/mata pelajaran:', error);
  }
};

const saveSchedule = async () => {
  try {
    if (props.schedule) {
      await axios.put(`/api/guru/schedules/${props.schedule.id}`, form);
    } else {
      await axios.post('/api/guru/schedules', form);
    }
    emit('schedule-saved');
    emit('close');
  } catch (error) {
    console.error('Gagal menyimpan jadwal:', error.response || error);
    alert('Gagal menyimpan jadwal. Silakan periksa data Anda.');
  }
};

watch(() => props.schedule, (newSchedule) => {
  if (newSchedule) {
    form.class_id = newSchedule.class_subject_teachers.class_id;
    form.subject_id = newSchedule.class_subject_teachers.subject_id;
    form.day_of_week = newSchedule.day_of_week;
    form.start_time = newSchedule.start_time;
    form.end_time = newSchedule.end_time;
  } else {
    form.class_id = '';
    form.subject_id = '';
    form.day_of_week = '';
    form.start_time = '';
    form.end_time = '';
  }
}, { immediate: true });

onMounted(() => {
    fetchInitialData();
});
</script>