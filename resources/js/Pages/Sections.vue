<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Header />
    <div class="flex flex-1 p-6">
      <Sidebar :active-section="activeSection" @section-change="handleSectionChange" />
      <main class="flex-1 ml-6 overflow-auto">
        <div class="bg-white p-6 rounded shadow">
          <Tabs :currentTab="currentTab" @tab-change="handleTabChange" />
          <div class="mt-6">
            <component :is="currentComponent" />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Tabs from '../Components/Tabs.vue';
import Incoming from './Incoming.vue';
import Outgoing from './Outgoing.vue';
import { router } from '@inertiajs/vue3';

const tabMap = {
  incoming: Incoming,
  outgoing: Outgoing
};

const currentTab = ref('incoming');
const activeSection = ref('incoming');

const currentComponent = computed(() => tabMap[currentTab.value]);

function handleTabChange(tab) {
  currentTab.value = tab;
  activeSection.value = tab;
  router.visit(`/sections?tab=${tab}`);
}

function handleSectionChange(newSection) {
  handleTabChange(newSection);
}
</script> 