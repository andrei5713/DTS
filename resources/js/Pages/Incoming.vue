<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Incoming Documents</h2>
        <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search documents..." />
    </div>
    
    <!-- Approval Queue Table -->
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Approval Queue</h3>
        <p class="text-sm text-gray-600 mt-1">Documents pending your approval</p>
      </div>
      <Table :columns="approvalColumns" :rows="pendingDocuments">
        <template #ACTIONS="{ row }">
          <div class="flex gap-2">
            <button 
              @click="viewDocument(row)"
              class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300"
            >
              View
            </button>
            <button 
              v-if="row.current_recipient_id === currentUser?.id && canPerformActions(row)"
              @click="acceptDocument(row.id)"
              :disabled="acceptingId === row.id"
              class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-green-300 disabled:opacity-50"
            >
              Accept
            </button>
            <button 
              v-if="row.current_recipient_id === currentUser?.id && canPerformActions(row)"
              @click="openRespondModal(row)"
              class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-purple-300"
            >
              Respond
            </button>
            <button 
              v-if="row.current_recipient_id === currentUser?.id && canPerformActions(row)"
              @click="rejectDocument(row.id)"
              :disabled="rejectingId === row.id"
              class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-red-300 disabled:opacity-50"
            >
              Reject
            </button>
            <div v-else-if="row.current_recipient_id === currentUser?.id && !canPerformActions(row)" class="text-gray-500 text-xs">
              Only DO of forwarded unit can perform actions
            </div>
          </div>
        </template>
      </Table>
      <div v-if="pendingDocuments.length === 0" class="px-6 py-4 text-center text-sm text-gray-500">
        No documents pending approval
      </div>
    </div>

    <!-- Received Documents Table -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
        <h3 class="text-lg font-medium text-gray-900">Received Documents</h3>
        <p class="text-sm text-gray-600 mt-1">Documents you have accepted</p>
          </div>
          <div class="flex items-center space-x-1">
            <button 
              @click="receivedViewMode = 'table'"
              :class="[
                'p-2 rounded-md transition-colors',
                receivedViewMode === 'table' 
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
              @click="receivedViewMode = 'cards'"
              :class="[
                'p-2 rounded-md transition-colors',
                receivedViewMode === 'cards' 
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
      </div>
      <!-- Table View -->
      <div v-if="receivedViewMode === 'table'">
      <Table :columns="receivedColumns" :rows="receivedDocuments">
        <template #ACTIONS="{ row }">
                      <div class="flex gap-2">
              <button 
                @click="viewDocument(row)"
                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
                View
              </button>
              <button 
                @click="complyDocument(row.id)"
                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-orange-300"
              >
                Complied
              </button>
              <span class="text-green-600 text-sm font-medium">Accepted</span>
            </div>
        </template>
      </Table>
      </div>
      
      <!-- Card View -->
      <div v-else-if="receivedViewMode === 'cards'" class="p-6">
        <div v-if="receivedDocuments.length === 0" class="text-center py-12">
          <div class="text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No received documents</h3>
          <p class="text-sm text-gray-500">Documents you accept will appear here.</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="document in receivedDocuments" 
            :key="document.id"
            class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-3">
              <div class="flex-1">
                <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ document.subject }}</h4>
                <p class="text-xs text-gray-600">{{ document.tracking_code }}</p>
              </div>
              <span class="px-2 py-1 text-xs font-medium bg-green-200 text-green-800 rounded-full">
                Accepted
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
                <span class="text-gray-500">Priority:</span>
                <span class="text-gray-700">{{ document.priority }}</span>
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
                @click="complyDocument(document.id)"
                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-md text-xs font-medium transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-orange-300"
              >
                Complied
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="receivedViewMode === 'table' && receivedDocuments.length === 0" class="px-6 py-4 text-center text-sm text-gray-500">
        No received documents
      </div>
    </div>

    <!-- Respond Modal -->
    <div v-if="showRespondModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-black bg-opacity-50" @click="showRespondModal = false"></div>
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Respond to Document</h3>
          </div>
          <div class="p-6">
            <!-- Document Preview (same as view modal) -->
            <div class="mb-6">
              <h4 class="text-md font-medium text-gray-900 mb-4">Document Details</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                  <input 
                    :value="respondDocument?.subject" 
                    readonly 
                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700"
                  />
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
                  <input 
                    :value="respondDocument?.document_type" 
                    readonly 
                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700"
                  />
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                  <input 
                    :value="respondDocument?.priority" 
                    readonly 
                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700"
                  />
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <input 
                    :value="respondDocument?.status" 
                    readonly 
                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700"
                  />
                </div>
              </div>
            </div>

            <!-- Respond Form -->
            <form @submit.prevent="submitResponse" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Respond To *</label>
                <input 
                  :value="`${respondDocument?.upload_by_user?.name || 'Unknown'} - ${respondDocument?.upload_by_user?.unit?.full_name || 'No Unit'}`"
                  readonly
                  class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Response Message *</label>
              <textarea 
                  v-model="respondForm.response_message" 
                  rows="4"
                  required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Type your response message here..."
              ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" @click="showRespondModal = false" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                Cancel
              </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                  Send Response
              </button>
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>

    <!-- PDF Preview Modal -->
    <EnhancedPdfViewer 
      v-if="showPdfModal" 
      :document="pdfDocument" 
      :pdf-url="pdfUrl" 
      @close="closePdfModal" 
    />

    <!-- Notification -->
    <Notification 
      :show="showNotification"
      :message="notificationMessage"
      :type="notificationType"
      @close="showNotification = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import SearchBar from '@/Components/SearchBar.vue';
import EnhancedPdfViewer from '@/Components/EnhancedPdfViewer.vue';
import Notification from '@/Components/Notification.vue';

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

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


const documents = ref([]);
const searchQuery = ref('');
const receivedViewMode = ref('table');
const showRespondModal = ref(false);
const respondDocument = ref(null);
const acceptingId = ref(null);
const rejectingId = ref(null);
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref('info');

const respondForm = ref({
  response_message: ''
});

const approvalColumns = [
  { label: 'SUBJECT/TITLE', key: 'subject' },
  { label: 'UPLOAD BY', key: 'upload_by' },
  { label: 'UNIT', key: 'unit' },
  { label: 'ACTIONS', key: 'ACTIONS' },
];

const receivedColumns = [
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

// Separate documents into pending and received with search functionality
const pendingDocuments = computed(() => {
  let docs = transformedDocuments.value.filter(doc => doc.status === 'pending');
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

const receivedDocuments = computed(() => {
  let docs = transformedDocuments.value.filter(doc => doc.status === 'received');
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
    const response = await fetch('/documents/incoming?all=1');
    const data = await response.json();
    documents.value = data.documents || [];
    
    // Debug logging
    console.log('Fetched documents:', {
      totalDocuments: documents.value.length,
      documents: documents.value.map(doc => ({
        id: doc.id,
        subject: doc.subject,
        current_recipient_id: doc.current_recipient_id,
        upload_to_user_id: doc.upload_to_user_id,
        upload_to_user: doc.upload_to_user,
        status: doc.status
      }))
    });
  } catch (error) {
    console.error('Error fetching documents:', error);
  }
}




function openRespondModal(document) {
  respondDocument.value = document;
  showRespondModal.value = true;
  respondForm.value.response_message = '';
}
const showPdfModal = ref(false);
const pdfDocument = ref(null);
const pdfUrl = ref("");

// Check if user can perform actions on a document
function canPerformActions(document) {
  if (!currentUser.value || !document) return false;
  
  // User must be the current recipient
  if (document.current_recipient_id !== currentUser.value.id) return false;
  
  // User must be from a DO unit
  const userUnit = currentUser.value.unit?.full_name || '';
  if (!userUnit.endsWith('/DO')) return false;
  
  // Debug logging
  console.log('canPerformActions check:', {
    documentId: document.id,
    currentUserId: currentUser.value.id,
    documentCurrentRecipientId: document.current_recipient_id,
    userUnit: userUnit,
    isCurrentRecipient: document.current_recipient_id === currentUser.value.id,
    isDO: userUnit.endsWith('/DO')
  });
  
  // Simplified logic: If user is current recipient and is from a DO unit, allow actions
  // This matches the backend logic and ensures DO users can see and act on documents
  return true;
}

function closePdfModal() {
  showPdfModal.value = false;
  pdfUrl.value = "";
  pdfDocument.value = null;
}

function complyDocument(documentId) {
  // TODO: Implement comply functionality
  console.log('Comply document:', documentId);
  alert('Comply functionality will be implemented soon.');
}

function showNotificationMessage(message, type = 'info') {
  notificationMessage.value = message;
  notificationType.value = type;
  showNotification.value = true;
}

function viewDocument(document) {
  // Show PDF preview modal
  pdfDocument.value = document;
  if (document.file_path) {
    pdfUrl.value = `/storage/${document.file_path}`;
  } else {
    pdfUrl.value = "";
  }
  showPdfModal.value = true;
}

async function submitResponse() {
  try {
    const csrfToken = getCSRFToken();
    console.log('CSRF Token for respond:', csrfToken);
    
    if (!csrfToken) {
      showNotificationMessage('CSRF token not found. Please refresh the page and try again.', 'error');
      return;
    }
    
    const response = await fetch(`/documents/${respondDocument.value.id}/respond`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify(respondForm.value)
    });
    
    if (response.ok) {
      const data = await response.json();
      showRespondModal.value = false;
      respondForm.value.response_message = '';
      showNotificationMessage(data.message || 'Response sent successfully', 'success');
      await fetchDocuments(); // Refresh the documents list
    } else {
      const errorData = await response.json().catch(() => ({}));
      showNotificationMessage(errorData.message || 'Error sending response', 'error');
    }
  } catch (error) {
    console.error('Error sending response:', error);
    showNotificationMessage('Error sending response. Please try again.', 'error');
  }
}

async function acceptDocument(documentId) {
  acceptingId.value = documentId;
  try {
    const csrfToken = getCSRFToken();
    console.log('CSRF Token for accept:', csrfToken);
    
    if (!csrfToken) {
      showNotificationMessage('CSRF token not found. Please refresh the page and try again.', 'error');
      return;
    }
    
    const response = await fetch(`/documents/${documentId}/receive`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    });
    if (response.ok) {
      const data = await response.json();
      // Show success message with automatic forwarding info
      if (data.message) {
        showNotificationMessage(data.message, 'success');
      }
      await fetchDocuments();
    } else {
      const data = await response.json().catch(() => ({}));
      showNotificationMessage(data.message || 'Error accepting document', 'error');
    }
  } catch (error) {
    console.error('Error accepting document:', error);
    showNotificationMessage('Error accepting document', 'error');
  } finally {
    acceptingId.value = null;
  }
}

async function rejectDocument(documentId) {
  if (!confirm('Are you sure you want to reject this document?')) {
    return;
  }
  rejectingId.value = documentId;
  try {
    const csrfToken = getCSRFToken();
    console.log('CSRF Token for reject:', csrfToken);
    
    if (!csrfToken) {
      showNotificationMessage('CSRF token not found. Please refresh the page and try again.', 'error');
      return;
    }
    
    const response = await fetch(`/documents/${documentId}/reject`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    });
    if (response.ok) {
      await fetchDocuments();
    } else {
      const data = await response.json().catch(() => ({}));
      alert(data.message || 'Error rejecting document');
    }
  } catch (error) {
    console.error('Error rejecting document:', error);
    alert('Error rejecting document');
  } finally {
    rejectingId.value = null;
  }
}

let pollTimer = null;

onMounted(() => {
  fetchDocuments();
  // Poll every 5 seconds for near-instant updates
  pollTimer = setInterval(fetchDocuments, 5000);
});

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer);
});
</script> 