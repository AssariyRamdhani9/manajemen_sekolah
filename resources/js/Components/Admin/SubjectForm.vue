<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-2xl font-semibold leading-tight text-gray-900 mb-4">
          {{ subject ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran Baru' }}
        </h3>
        
        <form @submit.prevent="saveSubject" class="mt-6 space-y-4">
          
          <div class="text-left">
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Mata Pelajaran</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required
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
import { reactive, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  subject: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'subject-saved']);

const form = reactive({
  name: '',
});

const saveSubject = async () => {
  try {
    if (props.subject) {
      await axios.put(`/api/admin/subjects/${props.subject.id}`, form);
    } else {
      await axios.post('/api/admin/subjects', form);
    }
    emit('subject-saved');
    emit('close');
  } catch (error) {
    console.error('Gagal menyimpan data mata pelajaran:', error.response || error);
    alert('Gagal menyimpan data. Pastikan nama mata pelajaran unik.');
  }
};

watch(() => props.subject, (newSubject) => {
  if (newSubject) {
    form.name = newSubject.name;
  } else {
    form.name = '';
  }
}, { immediate: true });
</script>