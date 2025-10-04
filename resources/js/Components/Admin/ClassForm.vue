<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-2xl font-semibold leading-tight text-gray-900 mb-4">
          {{ classData ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
        </h3>
        
        <form @submit.prevent="saveClass" class="mt-6 space-y-4">
          
          <div class="text-left">
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kelas</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required
            />
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
          </div>
          
          <div class="text-left">
            <label for="level" class="block text-sm font-medium text-gray-700">Jenjang</label>
            <input
              type="text"
              id="level"
              v-model="form.level"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="Contoh: X, XI, XII"
            />
          </div>
          
          <div class="text-left">
            <label for="major" class="block text-sm font-medium text-gray-700">Jurusan</label>
            <input
              type="text"
              id="major"
              v-model="form.major"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="Contoh: IPA, IPS"
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
import { reactive, watch, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  classData: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'class-saved']);

const form = reactive({
  name: '',
  level: '',
  major: '',
});

const errors = reactive({
  name: '',
});

const saveClass = async () => {
  try {
    errors.name = '';
    if (props.classData) {
      await axios.put(`/api/admin/classes/${props.classData.id}`, form);
    } else {
      await axios.post('/api/admin/classes', form);
    }
    emit('class-saved');
    emit('close');
  } catch (error) {
    console.error('Gagal menyimpan data kelas:', error.response || error);
    if (error.response && error.response.status === 422) {
      errors.name = error.response.data.errors.name ? error.response.data.errors.name[0] : '';
    } else {
      alert('Gagal menyimpan data. Pastikan nama kelas unik.');
    }
  }
};

watch(() => props.classData, (newClassData) => {
  if (newClassData) {
    form.name = newClassData.name;
    form.level = newClassData.level;
    form.major = newClassData.major;
  } else {
    form.name = '';
    form.level = '';
    form.major = '';
  }
}, { immediate: true });
</script>