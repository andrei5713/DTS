<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Rejected Documents</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search Rejected Documents" />
    </div>
    <Table :columns="columns" :rows="filteredRows">
      <template #ACTIONS="{ row }">
        <div class="flex gap-2">
          <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300" @click="viewDocument(row)">View</button>
          <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-orange-300" @click="resubmitDocument(row)">Resubmit</button>
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
    trackingCode: 'DOC-004',
    documentType: 'Request',
    subject: 'Equipment Purchase Request',
    documentDate: '2024-01-08',
    entryDate: '2024-01-09',
    sender: 'Sarah Wilson',
    departmentDivision: 'Operations Department',
    originType: 'Internal',
    priority: 'High',
    remarks: 'Insufficient budget justification',
    routing: 'Department Head → Admin',
    file: 'request_001.pdf',
    status: 'rejected',
    rejectedDate: '2024-01-10',
    rejectionReason: 'Budget constraints'
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
  { label: 'REJECTION REASON', key: 'rejectionReason' },
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

const resubmitDocument = (document) => {
  console.log('Resubmitting document:', document.trackingCode);
  // Add resubmit logic here
};
</script>
