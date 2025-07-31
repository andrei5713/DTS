<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Incoming Documents</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search Incoming Documents" />
    </div>
    <Table :columns="columns" :rows="filteredRows">
      <template #ACTIONS="{ row }">
        <div class="flex gap-2">
          <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300">Receive</button>
          <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-yellow-300">Forward</button>
        </div>
      </template>
    </Table>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Table from '@/Components/Table.vue';
import SearchBar from '@/Components/SearchBar.vue';

if (!window.incomingDocuments) window.incomingDocuments = [];
const rows = ref(window.incomingDocuments);

const searchQuery = ref('');

const columns = [
  { label: 'TRACKING CODE', key: 'trackingCode' },
  { label: 'DOCUMENT TYPE', key: 'documentType' },
  { label: 'SUBJECT', key: 'subject' },
  { label: 'DOCUMENT DATE', key: 'documentDate' },
  { label: 'DATE OF ENTRY', key: 'entryDate' },
  { label: 'SENDER', key: 'sender' },
  { label: 'DEPARTMENT/DIVISION', key: 'departmentDivision' },
  { label: 'ORIGIN TYPE', key: 'originType' },
  { label: 'PRIORITY', key: 'priority' },
  { label: 'REMARKS', key: 'remarks' },
  { label: 'ROUTING', key: 'routing' },
  { label: 'FILE', key: 'file' },
  { label: 'ACTIONS', key: 'ACTIONS' },
];

const filteredRows = computed(() => {
  if (!searchQuery.value.trim()) return rows.value;
  const q = searchQuery.value.toLowerCase();
  return rows.value.filter(row =>
    Object.values(row).some(val =>
      String(val).toLowerCase().includes(q)
    )
  );
});
</script> 