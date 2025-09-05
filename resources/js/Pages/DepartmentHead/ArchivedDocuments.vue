<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Archived Documents</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search archived documents..." />
      <div class="flex items-center space-x-1">
        <button 
          @click="viewMode = 'table'"
          :class="[
            'p-2 rounded-md transition-colors',
            viewMode === 'table' 
              ? 'bg-blue-500 text-white' 
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
          title="Table View"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a1 1 0 011-1h16a1 1 0 011 1v16a1 1 0 01-1 1H4a1 1 0 01-1-1z"></path>
          </svg>
        </button>
        <button 
          @click="viewMode = 'cards'"
          :class="[
            'p-2 rounded-md transition-colors',
            viewMode === 'cards' 
              ? 'bg-blue-500 text-white' 
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
          title="Card View"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Archived Documents Content -->
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Archived Documents</h3>
        <p class="text-sm text-gray-600 mt-1">Documents that have been archived</p>
      </div>
      
      <!-- Table View -->
      <div v-if="viewMode === 'table'">
        <Table :columns="archivedColumns" :rows="filteredArchivedDocuments">
          <template #ACTIONS="{ row }">
            <div class="flex gap-2">
              <button 
                @click="viewDocument(row)"
                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
                View
              </button>
              <button 
                @click="downloadDocument(row)"
                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                Download
              </button>
            </div>
          </template>
        </Table>
      </div>
      
      <!-- Card View -->
      <div v-else-if="viewMode === 'cards'" class="p-6">
        <div v-if="filteredArchivedDocuments.length === 0" class="text-center py-12">
          <div class="text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No archived documents</h3>
          <p class="text-sm text-gray-500">Documents will appear here once they have been archived.</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="document in filteredArchivedDocuments" 
            :key="document.id"
            class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-3">
              <div class="flex-1">
                <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ document.subject }}</h4>
                <p class="text-xs text-gray-600">{{ document.tracking_code }}</p>
              </div>
              <span class="px-2 py-1 text-xs font-medium bg-gray-200 text-gray-800 rounded-full">
                {{ document.priority }}
              </span>
            </div>
            
            <div class="space-y-2 mb-4">
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">Type:</span>
                <span class="text-gray-700">{{ document.document_type }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">From:</span>
                <span class="text-gray-700">{{ document.upload_by }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">To:</span>
                <span class="text-gray-700">{{ document.upload_to }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">Office:</span>
                <span class="text-gray-700">{{ document.originating_office }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">Status:</span>
                <span class="text-gray-700 capitalize">{{ document.status }}</span>
              </div>
            </div>
            
            <div class="flex gap-2">
              <button 
                @click="viewDocument(document)"
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-md text-xs font-medium transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
                View
              </button>
              <button 
                @click="downloadDocument(document)"
                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-md text-xs font-medium transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                Download
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="viewMode === 'table' && filteredArchivedDocuments.length === 0" class="px-6 py-12 text-center">
        <div class="text-gray-400 mb-4">
          <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No archived documents</h3>
        <p class="text-sm text-gray-500">Documents will appear here once they have been archived.</p>
      </div>
    </div>

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
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import SearchBar from '@/Components/SearchBar.vue';
import EnhancedPdfViewer from '@/Components/EnhancedPdfViewer.vue';

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

// Get CSRF token with fallback
function getCSRFToken() {
  if (page.props.csrf_token) {
    return page.props.csrf_token;
  }
  
  const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (metaToken) {
    return metaToken;
  }
  
  console.warn('No CSRF token found');
  return '';
}

const documents = ref([]);
const searchQuery = ref('');
const viewMode = ref('table');
const showPdfModal = ref(false);
const pdfDocument = ref(null);
const pdfUrl = ref('');

const archivedColumns = [
  { label: 'TRACKING CODE', key: 'tracking_code' },
  { label: 'DOCUMENT TYPE', key: 'document_type' },
  { label: 'SUBJECT', key: 'subject' },
  { label: 'UPLOAD BY', key: 'upload_by' },
  { label: 'UPLOAD TO', key: 'upload_to' },
  { label: 'ORIGINATING OFFICE', key: 'originating_office' },
  { label: 'PRIORITY', key: 'priority' },
  { label: 'STATUS', key: 'status' },
  { label: 'ACTIONS', key: 'ACTIONS' },
];

// Transform documents to include unit field for display
const transformedDocuments = computed(() => {
  return documents.value.map(doc => ({
    ...doc,
    unit: doc.upload_to_user?.unit?.full_name || 'N/A'
  }));
});

// Filter archived documents with search functionality
const filteredArchivedDocuments = computed(() => {
  let docs = transformedDocuments.value.filter(doc => doc.status === 'archived');
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    docs = docs.filter(doc =>
      Object.values(doc).some(val =>
        String(val).toLowerCase().includes(q)
      )
    );
  }
  return docs;
});

async function fetchDocuments() {
  try {
    const response = await fetch('/documents/archived', {
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    });
    const data = await response.json();
    documents.value = data.documents || [];
  } catch (error) {
    console.error('Error fetching archived documents:', error);
  }
}

function viewDocument(document) {
  pdfDocument.value = document;
  if (document.file_path) {
    pdfUrl.value = `/storage/${document.file_path}`;
  } else {
    pdfUrl.value = "";
  }
  showPdfModal.value = true;
}

function downloadDocument(document) {
  if (document.file_path) {
    const link = document.createElement('a');
    link.href = `/storage/${document.file_path}`;
    link.download = document.file_name || 'document.pdf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
}

function closePdfModal() {
  showPdfModal.value = false;
  pdfDocument.value = null;
  pdfUrl.value = "";
}

onMounted(() => {
  fetchDocuments();
});
</script>
