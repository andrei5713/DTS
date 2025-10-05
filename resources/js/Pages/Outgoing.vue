<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Outgoing Documents</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search Outgoing Documents" />
      <div class="flex items-center space-x-4">
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
        <button 
          v-if="canUpload"
          class="bg-blue-600 text-white px-4 py-2 rounded" 
          @click="showUploadModal = true"
        >
          Upload Document
        </button>
        <div v-else class="text-gray-500 text-sm">
          Only users from Director's Office units can upload documents.
        </div>
      </div>
    </div>
    <!-- Table View -->
    <div v-if="viewMode === 'table'">
      <Table :columns="columns" :rows="documents">
        <template #ACTIONS="{ row }">
          <div class="flex gap-2">
            <button 
              v-if="row.status === 'received'"
              @click="openResponseModal(row)"
              class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-300"
            >
              Response
            </button>
            <span v-if="row.status === 'pending'" class="text-yellow-600 text-sm">Pending</span>
            <span v-else-if="row.status === 'received'" class="text-green-600 text-sm">Received</span>
            <span v-else-if="row.status === 'forwarded'" class="text-blue-600 text-sm">Forwarded</span>
            <span v-else-if="row.status === 'rejected'" class="text-red-600 text-sm">Rejected</span>
          </div>
        </template>
      </Table>
    </div>
    
    <!-- Card View -->
    <div v-else-if="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="document in documents" 
        :key="document.id"
        class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
      >
        <div class="flex justify-between items-start mb-3">
          <div class="flex-1">
            <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ document.subject }}</h4>
            <p class="text-xs text-gray-600">{{ document.tracking_code }}</p>
          </div>
          <span 
            :class="[
              'px-2 py-1 text-xs font-medium rounded-full',
              document.status === 'pending' ? 'bg-yellow-200 text-yellow-800' :
              document.status === 'received' ? 'bg-green-200 text-green-800' :
              document.status === 'forwarded' ? 'bg-blue-200 text-blue-800' :
              document.status === 'rejected' ? 'bg-red-200 text-red-800' :
              'bg-gray-200 text-gray-800'
            ]"
          >
            {{ document.status }}
          </span>
        </div>
        
        <div class="space-y-2 mb-4">
          <div class="flex justify-between text-xs">
            <span class="text-gray-500">Type:</span>
            <span class="text-gray-700">{{ document.document_type }}</span>
          </div>
          <div class="flex justify-between text-xs">
            <span class="text-gray-500">Sent To:</span>
            <span class="text-gray-700">{{ document.upload_to }}</span>
          </div>
          <div class="flex justify-between text-xs">
            <span class="text-gray-500">Office:</span>
            <span class="text-gray-700">{{ document.originating_office }}</span>
          </div>
          <div class="flex justify-between text-xs">
            <span class="text-gray-500">Priority:</span>
            <span class="text-gray-700">{{ document.priority }}</span>
          </div>
          <div class="flex justify-between text-xs">
            <span class="text-gray-500">Date:</span>
            <span class="text-gray-700">{{ document.entry_date }}</span>
          </div>
          <div v-if="document.status === 'rejected' && document.rejection_reason" class="flex justify-between text-xs">
            <span class="text-gray-500">Rejection Reason:</span>
            <span class="text-red-700 font-medium">{{ document.rejection_reason }}</span>
          </div>
        </div>
        
        <div class="flex gap-2">
          <button 
            v-if="document.status === 'received'"
            @click="openResponseModal(document)"
            class="flex-1 bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-2 rounded-md text-xs font-medium transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-300"
          >
            Response
          </button>
        </div>
      </div>
    </div>
    <UploadModal 
      :show="showUploadModal" 
      title="Upload Outgoing Document" 
      @close="showUploadModal = false; editIndex = null; editFormData = null;" 
      @upload="handleUpload" 
      :formData="editFormData"
      :units="units"
      :currentUser="currentUser"
    />
    <!-- Notification -->
    <Notification 
      :show="showNotification"
      :message="notificationMessage"
      :type="notificationType"
      @close="showNotification = false"
    />

    <!-- Response Modal -->
    <ResponseModal 
      :show="showResponseModal"
      :document="responseDocument"
      @close="showResponseModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import UploadModal from '@/Components/UploadModal.vue';
import SearchBar from '@/Components/SearchBar.vue';
import { useDeleteAlert } from '@/composables/useDeleteAlert.js';
import Notification from '@/Components/Notification.vue';
import ResponseModal from '@/Components/ResponseModal.vue';

const documents = ref([]);

const showUploadModal = ref(false);
const searchQuery = ref('');
const viewMode = ref('table');
const editIndex = ref(null);
const editFormData = ref(null);
const units = ref([]);
const canUpload = ref(false);
const { confirmDelete } = useDeleteAlert();

// Notification state
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref('info');

// Response modal state
const showResponseModal = ref(false);
const responseDocument = ref(null);

let pollTimer = null;

// Get current user from page props
const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

const columns = [
  { label: 'TRACKING CODE', key: 'tracking_code' },
  { label: 'DOCUMENT TYPE', key: 'document_type' },
  { label: 'SUBJECT', key: 'subject' },
  { label: 'UPLOADED BY', key: 'upload_by' },
  { label: 'SENT TO', key: 'upload_to' },
  { label: 'ORIGINATING OFFICE', key: 'originating_office' },
  { label: 'PRIORITY', key: 'priority' },
  { label: 'STATUS', key: 'status' },
  { label: 'REJECTION REASON', key: 'rejection_reason' },
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

async function fetchDocuments() {
  try {
    const response = await fetch('/documents/outgoing');
    const data = await response.json();
    documents.value = data.documents || [];
  } catch (error) {
    console.error('Error fetching documents:', error);
  }
}

async function checkCanUpload() {
  try {
    const response = await fetch(route('api.can-upload'));
    const data = await response.json();
    canUpload.value = data.can_upload;
  } catch (error) {
    console.error('Error checking upload permission:', error);
  }
}

// Get CSRF token with fallback
function getCSRFToken() {
  // Try page props first
  if (page.props.csrf_token) {
    return page.props.csrf_token;
  }
  
  // Fallback to meta tag
  const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (metaToken) {
    return metaToken;
  }
  
  // If no token found, return empty string
  console.warn('No CSRF token found');
  return '';
}

async function handleUpload(uploadData) {
  try {
    const csrfToken = getCSRFToken();
    console.log('CSRF Token for upload:', csrfToken);
    
    if (!csrfToken) {
      alert('CSRF token not found. Please refresh the page and try again.');
      return;
    }
    
    const formData = new FormData();
    
    // Map camelCase field names to snake_case for backend
    const fieldMapping = {
      'trackingCode': 'tracking_code',
      'documentType': 'document_type',
      'entryDate': 'entry_date',
      'uploadBy': 'upload_by',
      'uploadTo': 'upload_to',
      'originatingOffice': 'originating_office',
      'forwardToDepartment': 'forward_to_department',
      'originType': 'origin_type',
      'routing': 'routing'
    };
    
    // Add all form data with proper field name mapping
    for (const [key, value] of uploadData.entries()) {
      if (key === 'file') {
        formData.append('file', value);
      } else if (fieldMapping[key]) {
        formData.append(fieldMapping[key], value);
      } else {
        formData.append(key, value);
      }
    }
    
    // Add document date if not present
    if (!formData.get('document_date')) {
      formData.append('document_date', new Date().toISOString().slice(0, 10));
    }
    
    // Debug: Log the form data being sent
    console.log('Form data being sent:');
    for (const [key, value] of formData.entries()) {
      console.log(`${key}: ${value}`);
    }
    
    const response = await fetch(route('documents.store'), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    });
    
    console.log('Response status:', response.status);
    
    if (response.ok) {
      const result = await response.json();
      if (result.success) {
        // Show success notification and refresh list
        showNotificationMessage(result.message || 'Document uploaded successfully.', 'success');
        await fetchDocuments();
        // Also notify other pages to refresh if needed
        window.dispatchEvent(new CustomEvent('refreshIncomingDocuments'));
        window.dispatchEvent(new CustomEvent('refreshOutgoingDocuments'));
      } else {
        alert('Error uploading document: ' + (result.message || 'Unknown error'));
      }
    } else {
      let errorMessage = 'Unknown error occurred';
      try {
        const errorData = await response.json();
        errorMessage = errorData.message || errorData.errors ? Object.values(errorData.errors).flat().join(', ') : 'Unknown error';
      } catch (e) {
        if (response.status === 419) {
          errorMessage = 'Session expired. Please refresh the page and try again.';
        } else if (response.status === 422) {
          errorMessage = 'Validation error. Please check your form data.';
        } else {
          errorMessage = `Server error (${response.status}). Please try again.`;
        }
      }
      alert('Error uploading document: ' + errorMessage);
    }
  } catch (error) {
    console.error('Upload error:', error);
    alert('Error uploading document. Please try again.');
  }
  
  showUploadModal.value = false;
}


function showNotificationMessage(message, type = 'info') {
  notificationMessage.value = message;
  notificationType.value = type;
  showNotification.value = true;
}

function openResponseModal(document) {
  responseDocument.value = document;
  showResponseModal.value = true;
}



onMounted(() => {
  fetchUnits();
  checkCanUpload();
  fetchDocuments();
  // Poll every 5 seconds for near-instant updates
  pollTimer = setInterval(() => {
    fetchDocuments();
  }, 5000);
});

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer);
});
</script> 