<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Outgoing Documents</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search Outgoing Documents" />
      <button class="bg-blue-600 text-white px-4 py-2 rounded" @click="showUploadModal = true">Upload Document</button>
    </div>
    <Table :columns="columns" :rows="rows">
      <template #ACTIONS="{ row, index }">
        <div class="flex gap-2">
          <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-green-300" @click="editRow(index)">Edit</button>
          <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-red-300" @click="deleteRow(index)">Delete</button>
        </div>
      </template>
    </Table>
    <UploadModal :show="showUploadModal" title="Upload Outgoing Document" @close="showUploadModal = false" @upload="handleUpload" />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Table from '@/Components/Table.vue';
import UploadModal from '@/Components/UploadModal.vue';
import SearchBar from '@/Components/SearchBar.vue';
import { useDeleteAlert } from '@/composables/useDeleteAlert.js';

if (!window.outgoingDocuments) window.outgoingDocuments = [];
const rows = ref(window.outgoingDocuments);

const showUploadModal = ref(false);
const searchQuery = ref('');
const { confirmDelete } = useDeleteAlert();

const columns = [
  { label: 'TRACKING CODE', key: 'trackingCode' },
  { label: 'DOCUMENT TYPE', key: 'documentType' },
  { label: 'SUBJECT', key: 'subject' },
  { label: 'DOCUMENT DATE', key: 'documentDate' },
  { label: 'DATE OF ENTRY', key: 'entryDate' },
  { label: 'SENDER', key: 'sender' },
  { label: 'ORIGINATING OFFICE', key: 'originatingOffice' },
  { label: 'ORIGIN TYPE', key: 'originType' },
  { label: 'PRIORITY', key: 'priority' },
  { label: 'REMARKS', key: 'remarks' },
  { label: 'ROUTING', key: 'routing' },
  { label: 'FILE', key: 'file' },
  { label: 'ACTIONS', key: 'ACTIONS' },
];

function handleUpload(uploadData) {
  const file = uploadData.get('file');
  rows.value.push({
    trackingCode: uploadData.get('trackingCode'),
    documentType: uploadData.get('documentType'),
    subject: uploadData.get('subject'),
    documentDate: uploadData.get('documentDate'),
    entryDate: uploadData.get('entryDate'),
    sender: uploadData.get('sender'),
    originatingOffice: uploadData.get('originatingOffice'),
    originType: uploadData.get('originType'),
    priority: uploadData.get('priority'),
    remarks: uploadData.get('remarks'),
    routing: uploadData.get('routing'),
    file: file ? file.name : '',
  });
}

function editRow(index) {
  // Implement edit logic here
  alert('Edit row ' + (index + 1));
}

async function deleteRow(index) {
  const confirmed = await confirmDelete();
  if (confirmed) {
    rows.value.splice(index, 1);
  }
}
</script> 