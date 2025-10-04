<template>
  <div class="min-h-screen flex bg-gray-100">
    <Sidebar role="admin" />

    <main class="flex-grow p-8 sm:p-12 transition-all duration-300">
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight mb-4 sm:mb-0">Manajemen Guru</h1>
        <button
          @click="openFormForNewTeacher"
          class="px-6 py-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Tambah Guru Baru
        </button>
      </header>

      <section class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="teachers.length > 0">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nama Lengkap
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  NIP
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Mata Pelajaran
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="teacher in teachers" :key="teacher.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ teacher.user.full_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ teacher.nip }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ teacher.subject_taught ? teacher.subject_taught.name : 'Belum Ditentukan' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="editTeacher(teacher)"
                    class="text-indigo-600 hover:text-indigo-900 mr-4"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteTeacher(teacher.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div v-else class="p-6 text-center text-gray-500">
          <p>Belum ada data guru yang tersedia.</p>
        </div>
      </section>
      
      <TeacherForm
        v-if="showForm"
        :teacher="selectedTeacher"
        @close="closeForm"
        @teacher-saved="fetchTeachers"
      />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Sidebar from '@/Components/common/Sidebar.vue';
import TeacherForm from '@/Components/Admin/TeacherForm.vue';

const teachers = ref([]);
const showForm = ref(false);
const selectedTeacher = ref(null);

const openFormForNewTeacher = () => {
  selectedTeacher.value = null;
  showForm.value = true;
};

const editTeacher = (teacher) => {
  selectedTeacher.value = teacher;
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  selectedTeacher.value = null;
};

const fetchTeachers = async () => {
  try {
    const response = await axios.get('/api/admin/teachers');
    teachers.value = response.data.teachers; 
  } catch (error) {
    console.error('Gagal mengambil data guru:', error.response || error);
    alert('Gagal mengambil data guru. Silakan coba lagi.');
  }
};

const deleteTeacher = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus guru ini? Tindakan ini tidak dapat dibatalkan.')) {
    try {
      await axios.delete(`/api/admin/teachers/${id}`);
      fetchTeachers();
    } catch (error) {
      console.error('Gagal menghapus guru:', error.response || error);
      alert('Gagal menghapus guru. Silakan coba lagi.');
    }
  }
};

onMounted(() => {
  fetchTeachers();
});
</script>