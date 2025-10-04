<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-2xl font-semibold leading-tight text-gray-900 mb-4">
          {{ student ? 'Edit Siswa' : 'Tambah Siswa Baru' }}
        </h3>
        
        <form @submit.prevent="saveStudent" class="mt-6 space-y-4">
          
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
          
          <div class="text-left">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :readonly="!!student"
              required
            />
          </div>
          
          <div class="text-left">
            <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
            <input
              type="text"
              id="nis"
              v-model="form.nis"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>
          
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
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
            <input
              type="date"
              id="date_of_birth"
              v-model="form.date_of_birth"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div class="text-left">
            <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
            <textarea
              id="address"
              v-model="form.address"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
          </div>

          <div class="text-left">
            <label for="parent_phone_number" class="block text-sm font-medium text-gray-700">Nomor Telepon Orang Tua</label>
            <input
              type="tel"
              id="parent_phone_number"
              v-model="form.parent_phone_number"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
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
  student: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'student-saved']);

const form = reactive({
  full_name: '',
  email: '',
  nis: '',
  class_id: '',
  date_of_birth: '',
  address: '',
  parent_phone_number: '',
});

const classes = ref([]);

const fetchClasses = async () => {
  try {
    const response = await axios.get('/api/admin/classes');
    classes.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil data kelas:', error);
  }
};

const saveStudent = async () => {
  try {
    if (props.student) {
      await axios.put(`/api/admin/students/${props.student.id}`, form);
    } else {
      await axios.post('/api/admin/students', form);
    }
    emit('student-saved');
    emit('close');
  } catch (error) {
    console.error('Gagal menyimpan data siswa:', error);
    alert('Gagal menyimpan data. Pastikan semua field terisi dengan benar.');
  }
};

watch(() => props.student, (newStudent) => {
  if (newStudent) {
    form.full_name = newStudent.user.full_name;
    form.email = newStudent.user.email;
    form.nis = newStudent.nis;
    form.class_id = newStudent.class_id;
    form.date_of_birth = newStudent.date_of_birth;
    form.address = newStudent.address;
    form.parent_phone_number = newStudent.parent_phone_number;
  } else {
    form.full_name = '';
    form.email = '';
    form.nis = '';
    form.class_id = '';
    form.date_of_birth = '';
    form.address = '';
    form.parent_phone_number = '';
  }
}, { immediate: true });

onMounted(() => {
  fetchClasses();
});
</script>