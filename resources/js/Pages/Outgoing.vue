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
    <UploadModal 
      :show="showUploadModal" 
      title="Upload Outgoing Document" 
      @close="showUploadModal = false; editIndex = null; editFormData = null;" 
      @upload="handleUpload" 
      :formData="editFormData"
      :units="units"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Table from '@/Components/Table.vue';
import UploadModal from '@/Components/UploadModal.vue';
import SearchBar from '@/Components/SearchBar.vue';
import { useDeleteAlert } from '@/composables/useDeleteAlert.js';

if (!window.outgoingDocuments) window.outgoingDocuments = [];
const rows = ref(window.outgoingDocuments);

const showUploadModal = ref(false);
const searchQuery = ref('');
const editIndex = ref(null);
const editFormData = ref(null);
const units = ref([]);
const { confirmDelete } = useDeleteAlert();

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

async function fetchUnits() {
  try {
    const response = await fetch(route('api.units'));
    const data = await response.json();
    units.value = data;
  } catch (error) {
    console.error('Error fetching units:', error);
  }
}

function handleUpload(uploadData) {
  const file = uploadData.get('file');
  const newRow = {
    trackingCode: uploadData.get('trackingCode'),
    documentType: uploadData.get('documentType'),
    subject: uploadData.get('subject'),
    documentDate: uploadData.get('documentDate'),
    entryDate: uploadData.get('entryDate'),
    sender: uploadData.get('sender'),
    departmentDivision: uploadData.get('departmentDivision'),
    originType: uploadData.get('originType'),
    priority: uploadData.get('priority'),
    remarks: uploadData.get('remarks'),
    routing: uploadData.get('routing'),
    file: file ? file.name : '',
  };
  if (editIndex.value !== null) {
    rows.value[editIndex.value] = newRow;
    editIndex.value = null;
    editFormData.value = null;
  } else {
    rows.value.push(newRow);
  }
  showUploadModal.value = false;
}

function editRow(index) {
  editIndex.value = index;
  editFormData.value = { ...rows.value[index] };
  showUploadModal.value = true;
}

async function deleteRow(index) {
  const confirmed = await confirmDelete();
  if (confirmed) {
    rows.value.splice(index, 1);
  }
}

onMounted(() => {
  fetchUnits();
});
</script> 