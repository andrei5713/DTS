<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Pending Approval</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search Pending Documents" />
      <div class="flex gap-2">
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" @click="approveAll">Approve All</button>
        <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" @click="rejectAll">Reject All</button>
      </div>
    </div>
    <Table :columns="columns" :rows="filteredRows">
      <template #ACTIONS="{ row }">
        <div class="flex gap-2">
          <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-green-300" @click="approveDocument(row)">Approve</button>
          <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-red-300" @click="rejectDocument(row)">Reject</button>
          <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300" @click="viewDocument(row)">View</button>
        </div>
      </template>
    </Table>

    <!-- PDF Preview Modal -->
    <EnhancedPdfViewer 
      v-if="showPdfModal" 
      :document="pdfDocument" 
      :pdf-url="pdfUrl" 
      @close="closePdfModal" 
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Table from '@/Components/Table.vue';
import SearchBar from '@/Components/SearchBar.vue';
import EnhancedPdfViewer from '@/Components/EnhancedPdfViewer.vue';

// Mock data - replace with actual data from backend
const rows = ref([
  {
    trackingCode: 'DOC-001',
    documentType: 'Memo',
    subject: 'Request for Office Supplies',
    documentDate: '2024-01-15',
    entryDate: '2024-01-16',
    sender: 'John Doe',
    departmentDivision: 'IT Department',
    originType: 'Internal',
    priority: 'High',
    remarks: 'Urgent request',
    routing: 'Department Head → Admin',
    file: 'memo_001.pdf',
    status: 'pending'
  },
  {
    trackingCode: 'DOC-002',
    documentType: 'Report',
    subject: 'Monthly Activity Report',
    documentDate: '2024-01-14',
    entryDate: '2024-01-15',
    sender: 'Jane Smith',
    departmentDivision: 'HR Department',
    originType: 'Internal',
    priority: 'Medium',
    remarks: 'Monthly submission',
    routing: 'Department Head → Admin',
    file: 'report_001.pdf',
    status: 'pending'
  }
]);

const searchQuery = ref('');
const showPdfModal = ref(false);
const pdfDocument = ref(null);
const pdfUrl = ref('');

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

const approveDocument = (document) => {
  console.log('Approving document:', document.trackingCode);
  // Add approval logic here
};

const rejectDocument = (document) => {
  console.log('Rejecting document:', document.trackingCode);
  // Add rejection logic here
};

const viewDocument = (document) => {
  pdfDocument.value = document;
  if (document.file_path) {
    pdfUrl.value = `/storage/${document.file_path}`;
  } else {
    pdfUrl.value = "";
  }
  showPdfModal.value = true;
};

const closePdfModal = () => {
  showPdfModal.value = false;
  pdfUrl.value = "";
  pdfDocument.value = null;
};

const approveAll = () => {
  console.log('Approving all documents');
  // Add bulk approval logic here
};

const rejectAll = () => {
  console.log('Rejecting all documents');
  // Add bulk rejection logic here
};
</script>
