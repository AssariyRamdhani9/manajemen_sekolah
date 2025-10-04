<template>
  <div class="min-h-screen flex bg-gray-100">
    <Sidebar role="admin" />

    <main class="flex-grow p-8 sm:p-12 transition-all duration-300">
      <header class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight">Manajemen Siswa</h1>
        <button
          @click="showForm = true; selectedStudent = null"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200"
        >
          Tambah Siswa Baru
        </button>
      </header>

      <section class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nama Lengkap
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                NIS
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Kelas
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="student in students" :key="student.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ student.user.full_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ student.nis }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ student.class.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="editStudent(student)"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Edit
                </button>
                <button
                  @click="deleteStudent(student.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
      
      <StudentForm
        v-if="showForm"
        :student="selectedStudent"
        @close="showForm = false; selectedStudent = null"
        @student-saved="fetchStudents"
      />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Sidebar from '@/Components/common/Sidebar.vue';
import StudentForm from '@/Components/Admin/StudentForm.vue';

const students = ref([]);
const showForm = ref(false);
const selectedStudent = ref(null);

const fetchStudents = async () => {
  try {
    const response = await axios.get('/api/admin/students');
    students.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil data siswa:', error);
  }
};

const editStudent = (student) => {
  selectedStudent.value = student;
  showForm.value = true;
};

const deleteStudent = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus siswa ini?')) {
    try {
      await axios.delete(`/api/admin/students/${id}`);
      fetchStudents(); // Muat ulang daftar siswa
    } catch (error) {
      console.error('Gagal menghapus siswa:', error);
    }
  }
};

onMounted(() => {
  fetchStudents();
});
</script>