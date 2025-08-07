<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Incoming Documents</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search Incoming Documents" />
    </div>
    <Table :columns="columns" :rows="filteredRows">
            <template #ACTIONS="{ row }">
        <div class="flex gap-2">
          <button 
            @click="viewDocument(row)"
            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300"
          >
            View
          </button>
          <button 
            v-if="row.status === 'pending' && row.current_recipient_id === currentUser?.id"
            @click="acceptDocument(row.id)"
            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-green-300"
          >
            Accept
          </button>
          <button 
            v-if="row.status === 'pending' && row.current_recipient_id === currentUser?.id"
            @click="openForwardModal(row)"
            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-yellow-300"
          >
            Forward
          </button>
          <button 
            v-if="row.status === 'pending' && row.current_recipient_id === currentUser?.id"
            @click="rejectDocument(row.id)"
            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-red-300"
          >
            Reject
          </button>
          <span v-else-if="row.status === 'received'" class="text-green-600 text-sm">Accepted</span>
          <span v-else-if="row.status === 'forwarded'" class="text-yellow-600 text-sm">Forwarded</span>
          <span v-else-if="row.status === 'rejected'" class="text-red-600 text-sm">Rejected</span>
        </div>
      </template>
    </Table>

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
                @blur="setTimeout(() => showUserSuggestions = false, 200)"
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import SearchBar from '@/Components/SearchBar.vue';

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

const documents = ref([]);
const searchQuery = ref('');
const showForwardModal = ref(false);
const selectedDocument = ref(null);
const users = ref([]);
const filteredUsers = ref([]);
const showUserSuggestions = ref(false);
const selectedUserIndex = ref(-1);

const forwardForm = ref({
  forward_to_user: '',
  forward_notes: ''
});

const columns = [
  { label: 'SUBJECT/TITLE', key: 'subject' },
  { label: 'UPLOAD BY', key: 'upload_by' },
  { label: 'UNIT', key: 'originating_office' },
  { label: 'ACTIONS', key: 'ACTIONS' },
];

const filteredRows = computed(() => {
  if (!searchQuery.value.trim()) return documents.value;
  const q = searchQuery.value.toLowerCase();
  return documents.value.filter(row =>
    Object.values(row).some(val =>
      String(val).toLowerCase().includes(q)
    )
  );
});

async function fetchDocuments() {
  try {
    const response = await fetch('/documents');
    const data = await response.json();
    documents.value = data.documents || [];
  } catch (error) {
    console.error('Error fetching documents:', error);
  }
}

async function fetchUsers(query = '') {
  try {
    const response = await fetch(`/api/users?q=${encodeURIComponent(query)}`);
    const data = await response.json();
    users.value = data;
    filteredUsers.value = data;
  } catch (error) {
    console.error('Error fetching users:', error);
  }
}

function searchUsers(query) {
  if (!query.trim()) {
    filteredUsers.value = [];
    showUserSuggestions.value = false;
    return;
  }
  
  filteredUsers.value = users.value.filter(user => 
    user.name.toLowerCase().includes(query.toLowerCase()) ||
    user.username.toLowerCase().includes(query.toLowerCase()) ||
    user.email.toLowerCase().includes(query.toLowerCase())
  );
  showUserSuggestions.value = filteredUsers.value.length > 0;
  selectedUserIndex.value = -1;
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

function openForwardModal(document) {
  selectedDocument.value = document;
  showForwardModal.value = true;
  fetchUsers();
}

function viewDocument(document) {
  // Show document details in a modal or navigate to document view
  alert(`Document: ${document.subject}\nUploaded by: ${document.upload_by}\nUnit: ${document.originating_office}`);
}

async function acceptDocument(documentId) {
  try {
    const response = await fetch(`/documents/${documentId}/receive`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    
    if (response.ok) {
      await fetchDocuments(); // Refresh the documents list
    } else {
      alert('Error accepting document');
    }
  } catch (error) {
    console.error('Error accepting document:', error);
    alert('Error accepting document');
  }
}

async function forwardDocument() {
  try {
    const response = await fetch(`/documents/${selectedDocument.value.id}/forward`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
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

async function rejectDocument(documentId) {
  if (!confirm('Are you sure you want to reject this document?')) {
    return;
  }
  
  try {
    const response = await fetch(`/documents/${documentId}/reject`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    
    if (response.ok) {
      await fetchDocuments(); // Refresh the documents list
    } else {
      alert('Error rejecting document');
    }
  } catch (error) {
    console.error('Error rejecting document:', error);
    alert('Error rejecting document');
  }
}

onMounted(() => {
  fetchDocuments();
});
</script> 