<template>
  <div class="min-h-screen flex bg-gray-100">
    <Sidebar role="admin" />

    <main class="flex-grow p-8 sm:p-12 transition-all duration-300">
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight mb-4 sm:mb-0">Manajemen Kelas</h1>
        <button
          @click="openFormForNewClass"
          class="px-6 py-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Tambah Kelas Baru
        </button>
      </header>

      <section class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="classes.length > 0">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nama Kelas
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Jenjang
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Jurusan
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="cls in classes" :key="cls.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ cls.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ cls.level || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ cls.major || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="editClass(cls)"
                    class="text-indigo-600 hover:text-indigo-900 mr-4"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteClass(cls.id)"
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
          <p>Belum ada data kelas yang tersedia.</p>
        </div>
      </section>
      
      <ClassForm
        v-if="showForm"
        :classData="selectedClass"
        @close="closeForm"
        @class-saved="fetchClasses"
      />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Sidebar from '@/Components/common/Sidebar.vue';
import ClassForm from '@/Components/Admin/ClassForm.vue';

const classes = ref([]);
const showForm = ref(false);
const selectedClass = ref(null);

const openFormForNewClass = () => {
  selectedClass.value = null;
  showForm.value = true;
};

const editClass = (cls) => {
  selectedClass.value = cls;
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  selectedClass.value = null;
};

const fetchClasses = async () => {
  try {
    const response = await axios.get('/api/admin/classes');
    classes.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil data kelas:', error.response || error);
    alert('Gagal mengambil data kelas. Silakan coba lagi.');
  }
};

const deleteClass = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus kelas ini? Tindakan ini tidak dapat dibatalkan.')) {
    try {
      await axios.delete(`/api/admin/classes/${id}`);
      fetchClasses();
    } catch (error) {
      console.error('Gagal menghapus kelas:', error.response || error);
      alert('Gagal menghapus kelas. Pastikan kelas tidak memiliki siswa atau mata pelajaran terkait.');
    }
  }
};

onMounted(() => {
  fetchClasses();
});
</script>