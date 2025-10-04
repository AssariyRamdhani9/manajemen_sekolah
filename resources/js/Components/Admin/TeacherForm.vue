<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-2xl font-semibold leading-tight text-gray-900 mb-4">
          {{ teacher ? 'Edit Guru' : 'Tambah Guru Baru' }}
        </h3>
        
        <form @submit.prevent="saveTeacher" class="mt-6 space-y-4">
          
          <!-- Nama Lengkap -->
          <div class="text-left">
            <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input
              type="text"
              id="full_name"
              v-model="form.full_name"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>
          
          <!-- Email -->
          <div class="text-left">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :readonly="!!teacher"
              required
            />
          </div>
          
          <!-- NIP -->
          <div class="text-left">
            <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
            <input
              type="text"
              id="nip"
              v-model="form.nip"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>
          
          <!-- Mata Pelajaran -->
          <div class="text-left">
            <label for="subject_taught_id" class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
            <select
              id="subject_taught_id"
              v-model="form.subject_taught_id"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="" disabled>Pilih Mata Pelajaran</option>
              <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.name }}</option>
            </select>
          </div>

          <!-- Password -->
          <div v-if="!teacher" class="text-left">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input
              type="password"
              id="password"
              v-model="form.password"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>
          
          <!-- Tombol -->
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
  teacher: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'teacher-saved']);

const form = reactive({
  full_name: '',
  email: '',
  nip: '',
  subject_taught_id: '',
  password: '', // tambahkan password
});

const subjects = ref([]);

const fetchSubjects = async () => {
  try {
    const response = await axios.get('/api/admin/subjects');
    subjects.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil data mata pelajaran:', error);
  }
};

const saveTeacher = async () => {
  try {
    if (props.teacher) {
      await axios.put(`/api/admin/teachers/${props.teacher.id}`, form);
    } else {
      await axios.post('/api/admin/teachers', form);
    }
    emit('teacher-saved');
    emit('close');
  } catch (error) {
    console.error('Gagal menyimpan data guru:', error.response || error);
    alert('Gagal menyimpan data. Pastikan semua field terisi dengan benar.');
  }
};

watch(() => props.teacher, (newTeacher) => {
  if (newTeacher) {
    form.full_name = newTeacher.user.full_name;
    form.email = newTeacher.user.email;
    form.nip = newTeacher.nip;
    form.subject_taught_id = newTeacher.subject_taught_id;
    form.password = ''; // jangan isi password saat edit
  } else {
    form.full_name = '';
    form.email = '';
    form.nip = '';
    form.subject_taught_id = '';
    form.password = '';
  }
}, { immediate: true });

onMounted(() => {
  fetchSubjects();
});
</script>
