<template>
  <div class="min-h-screen flex bg-gray-100">
    <Sidebar role="admin" />

    <main class="flex-grow p-8 sm:p-12 transition-all duration-300">
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight mb-4 sm:mb-0">Manajemen Mata Pelajaran</h1>
        <button
          @click="openFormForNewSubject"
          class="px-6 py-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Tambah Mata Pelajaran
        </button>
      </header>

      <section class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="subjects.length > 0">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nama Mata Pelajaran
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="subject in subjects" :key="subject.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ subject.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="editSubject(subject)"
                    class="text-indigo-600 hover:text-indigo-900 mr-4"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteSubject(subject.id)"
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
          <p>Belum ada data mata pelajaran yang tersedia.</p>
        </div>
      </section>
      
      <SubjectForm
        v-if="showForm"
        :subject="selectedSubject"
        @close="closeForm"
        @subject-saved="fetchSubjects"
      />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Sidebar from '@/Components/common/Sidebar.vue';
import SubjectForm from '@/Components/Admin/SubjectForm.vue';

const subjects = ref([]);
const showForm = ref(false);
const selectedSubject = ref(null);

const openFormForNewSubject = () => {
  selectedSubject.value = null;
  showForm.value = true;
};

const editSubject = (subject) => {
  selectedSubject.value = subject;
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  selectedSubject.value = null;
};

const fetchSubjects = async () => {
  try {
    const response = await axios.get('/api/admin/subjects');
    subjects.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil data mata pelajaran:', error.response || error);
    alert('Gagal mengambil data mata pelajaran. Silakan coba lagi.');
  }
};

const deleteSubject = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')) {
    try {
      await axios.delete(`/api/admin/subjects/${id}`);
      fetchSubjects();
    } catch (error) {
      console.error('Gagal menghapus mata pelajaran:', error.response || error);
      alert('Gagal menghapus mata pelajaran. Pastikan tidak ada guru yang mengajar mata pelajaran ini.');
    }
  }
};

onMounted(() => {
  fetchSubjects();
});
</script>
