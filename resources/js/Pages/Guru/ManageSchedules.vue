<template>
  <div class="min-h-screen flex bg-gray-100">
    <Sidebar role="teacher" />

    <main class="flex-grow p-8 sm:p-12 transition-all duration-300">
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight mb-4 sm:mb-0">Manajemen Jadwal</h1>
        <button
          @click="openFormForNewSchedule"
          class="px-6 py-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Tambah Jadwal
        </button>
      </header>
      
      <section class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Jadwal Mengajar Anda</h2>
        <div v-if="schedules.length > 0">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="schedule in schedules" :key="schedule.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ schedule.day_of_week }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ schedule.start_time }} - {{ schedule.end_time }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ schedule.class_subject_teacher.class.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ schedule.class_subject_teacher.subject.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="editSchedule(schedule)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                  <button @click="deleteSchedule(schedule.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="p-6 text-center text-gray-500">
          <p>Belum ada jadwal yang terdaftar.</p>
        </div>
      </section>
      
      <ScheduleForm
        v-if="showForm"
        :schedule="selectedSchedule"
        @close="closeForm"
        @schedule-saved="fetchSchedules"
      />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Sidebar from '@/Components/common/Sidebar.vue';
import ScheduleForm from '@/Components/Admin/ScheduleForm.vue';

const schedules = ref([]);
const showForm = ref(false);
const selectedSchedule = ref(null);

const openFormForNewSchedule = () => {
  selectedSchedule.value = null;
  showForm.value = true;
};

const editSchedule = (schedule) => {
  selectedSchedule.value = schedule;
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  selectedSchedule.value = null;
};

const fetchSchedules = async () => {
  try {
    const response = await axios.get('/api/teacher/schedules');
    schedules.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil jadwal:', error);
  }
};

const deleteSchedule = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
    try {
      await axios.delete(`/api/teacher/schedules/${id}`);
      fetchSchedules();
    } catch (error) {
      console.error('Gagal menghapus jadwal:', error);
    }
  }
};

onMounted(() => {
    fetchSchedules();
});
</script>