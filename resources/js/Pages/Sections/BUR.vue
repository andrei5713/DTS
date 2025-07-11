<script setup>
import { ref } from 'vue';
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
import BURTable from '@/Components/BURTable.vue';

const activeSection = ref('bur');
function handleSectionChange(newSection) {
  activeSection.value = newSection;
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Header />
        <div class="flex flex-1 p-6 ">
            <!-- Sidebar (left) -->
            <Sidebar :active-section="activeSection" @section-change="handleSectionChange" />

            <main class="flex-1 ml-6 overflow-auto">
        <div class="bg-white p-4 sm:p-6 rounded shadow w-full max-w-full">
          <h1 class="text-xl sm:text-2xl font-bold">BUR</h1>
          <p class="mb-4 text-sm sm:text-base text-gray-600">Manage budget utilization requests and ensure proper documentation and approval.</p>

          <!-- BUR Table -->
          <BURTable />
        </div>
      </main>
    </div>
  </div>
</template>
