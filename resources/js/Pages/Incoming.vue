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
              @click="openForwardModal(row)"
              class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-yellow-300"
            >
              Forward
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
        <h3 class="text-lg font-medium text-gray-900">Received Documents</h3>
        <p class="text-sm text-gray-600 mt-1">Documents you have accepted</p>
      </div>
      <Table :columns="receivedColumns" :rows="receivedDocuments">
        <template #ACTIONS="{ row }">
          <div class="flex gap-2">
            <button 
              @click="viewDocument(row)"
              class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300"
            >
              View
            </button>
            <span class="text-green-600 text-sm font-medium">Accepted</span>
          </div>
        </template>
      </Table>
      <div v-if="receivedDocuments.length === 0" class="px-6 py-4 text-center text-sm text-gray-500">
        No received documents
      </div>
    </div>

    <!-- Forward Modal -->
    <div v-if="showForwardModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-black bg-opacity-50" @click="showForwardModal = false"></div>
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Forward Document</h3>
          </div>
          <form @submit.prevent="forwardDocument" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Forward To *</label>
              <input 
                v-model="forwardForm.forward_to_user" 
                type="text" 
                required
                @input="searchUsers($event.target.value)"
                @keydown="handleKeydown"
                @focus="showUserSuggestions = true"
                @blur="handleBlur"
                placeholder="Start typing to search users..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <!-- User suggestions dropdown -->
              <div v-if="showUserSuggestions && filteredUsers.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                <div 
                  v-for="(user, index) in filteredUsers" 
                  :key="user.id"
                  @click="selectUser(user)"
                  class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                  :class="{ 'bg-blue-100': index === selectedUserIndex }"
                >
                  <div class="font-medium">{{ user.name }}</div>
                  <div class="text-sm text-gray-600">{{ user.username }} • {{ user.email }}</div>
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
              <textarea 
                v-model="forwardForm.forward_notes" 
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Add any notes about forwarding this document..."
              ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
              <button type="button" @click="showForwardModal = false" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                Cancel
              </button>
              <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                Forward Document
              </button>
            </div>
          </form>
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

const documents = ref([]);
const searchQuery = ref('');
const showForwardModal = ref(false);
const selectedDocument = ref(null);
const users = ref([]);
const filteredUsers = ref([]);
let searchDebounce = null;
const showUserSuggestions = ref(false);
const selectedUserIndex = ref(-1);
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref('info');

const forwardForm = ref({
  forward_to_user: '',
  forward_notes: ''
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

async function fetchUsers(query = '') {
  try {
    const response = await fetch(`/api/users?q=${encodeURIComponent(query)}`, {
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    });
    const data = await response.json();
    users.value = data;
    filteredUsers.value = data;
  } catch (error) {
    console.error('Error fetching users:', error);
  }
}

function searchUsers(query) {
  const q = query.trim();
  if (searchDebounce) clearTimeout(searchDebounce);

  if (!q) {
    filteredUsers.value = [];
    showUserSuggestions.value = false;
    return;
  }

  // Debounce slightly for rapid typing but keep it snappy
  searchDebounce = setTimeout(async () => {
    await fetchUsers(q);

    // Filter users to only show those within the same department, excluding /DO units
    const currentUserUnit = currentUser.value?.unit?.full_name || '';
    const currentUserDepartment = currentUserUnit.split('/')[0];
    const lowerQ = q.toLowerCase();

    filteredUsers.value = users.value.filter(user => {
      const userDepartment = user.unit_name?.split('/')[0] || user.unit?.full_name?.split('/')[0] || '';
      if (userDepartment !== currentUserDepartment) return false;
      if ((user.unit_name || user.unit?.full_name || '').endsWith('/DO')) return false;
      return (
        (user.name || '').toLowerCase().includes(lowerQ) ||
        (user.username || '').toLowerCase().includes(lowerQ) ||
        (user.email || '').toLowerCase().includes(lowerQ)
      );
    });

    showUserSuggestions.value = true;
    selectedUserIndex.value = -1;
  }, 100);
}

function selectUser(user) {
  forwardForm.value.forward_to_user = user.name;
  showUserSuggestions.value = false;
  selectedUserIndex.value = -1;
}

function handleKeydown(event) {
  if (!showUserSuggestions.value) return
  
  if (event.key === 'ArrowDown') {
    event.preventDefault();
    selectedUserIndex.value = Math.min(selectedUserIndex.value + 1, filteredUsers.value.length - 1);
  } else if (event.key === 'ArrowUp') {
    event.preventDefault();
    selectedUserIndex.value = Math.max(selectedUserIndex.value - 1, -1);
  } else if (event.key === 'Enter') {
    event.preventDefault();
    if (selectedUserIndex.value >= 0 && filteredUsers.value[selectedUserIndex.value]) {
      selectUser(filteredUsers.value[selectedUserIndex.value]);
    }
  } else if (event.key === 'Escape') {
    showUserSuggestions.value = false;
    selectedUserIndex.value = -1;
  }
}

function handleBlur() {
  setTimeout(() => {
    showUserSuggestions.value = false;
    selectedUserIndex.value = -1;
  }, 200);
}

function openForwardModal(document) {
  selectedDocument.value = document;
  showForwardModal.value = true;
  fetchUsers();
}

const acceptingId = ref(null);
const rejectingId = ref(null);
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

async function forwardDocument() {
  try {
    const response = await fetch(`/documents/${selectedDocument.value.id}/forward`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify(forwardForm.value)
    });
    
    if (response.ok) {
      showForwardModal.value = false;
      forwardForm.value = { forward_to_user: '', forward_notes: '' };
      await fetchDocuments(); // Refresh the documents list
    } else {
      alert('Error forwarding document');
    }
  } catch (error) {
    console.error('Error forwarding document:', error);
    alert('Error forwarding document');
  }
}

async function acceptDocument(documentId) {
  acceptingId.value = documentId;
  try {
    const response = await fetch(`/documents/${documentId}/receive`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
    const response = await fetch(`/documents/${documentId}/reject`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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