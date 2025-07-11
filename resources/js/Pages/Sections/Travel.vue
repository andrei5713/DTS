<script setup>
import { ref, computed } from 'vue';
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Traveltable from '@/Components/TravelTable.vue';

const activeSection = ref('travel');
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
          <h1 class="text-xl sm:text-2xl font-bold">Travel</h1>
          <p class="mb-4 text-sm sm:text-base text-gray-600">Manage travel-related documents and ensure proper authorization and tracking.</p>

          <!-- Travel Table -->
          <Traveltable />
        </div>
      </main>
    </div>
  </div>
</template>
