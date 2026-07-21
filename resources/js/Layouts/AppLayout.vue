<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans">
    <div class="flex-1 flex overflow-hidden">
      <!-- Overlay Backdrop for Mobile -->
      <div 
        v-if="isSidebarOpen"
        @click="isSidebarOpen = false" 
        class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm lg:hidden transition-opacity"
      ></div>

      <!-- Modern Sidebar -->
      <Sidebar 
        :role="role" 
        :is-open="isSidebarOpen"
        @close-sidebar="isSidebarOpen = false" 
      />

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
        <!-- Top Navbar -->
        <Navbar 
          :role="role" 
          @toggle-sidebar="isSidebarOpen = !isSidebarOpen" 
        />

        <!-- Page Content View -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
          <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-6 text-center text-xs text-slate-400 border-t border-slate-200/60">
          <p>© 2025 Sekolahku. Hak Cipta Dilindungi Undang-Undang.</p>
        </footer>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from '@/Components/common/Sidebar.vue'
import Navbar from '@/Components/common/Navbar.vue'

defineProps({
  role: {
    type: String,
    required: true
  }
})

const isSidebarOpen = ref(false)
</script>
