<template>
  <div>
    <h2 class="text-2xl font-bold mb-2">Outgoing Documents</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search Outgoing Documents" />
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
    <Table :columns="columns" :rows="documents">
      <template #ACTIONS="{ row }">
        <div class="flex gap-2">
          <span v-if="row.status === 'pending'" class="text-yellow-600 text-sm">Pending</span>
          <span v-else-if="row.status === 'received'" class="text-green-600 text-sm">Received</span>
          <span v-else-if="row.status === 'forwarded'" class="text-blue-600 text-sm">Forwarded</span>
          <span v-else-if="row.status === 'rejected'" class="text-red-600 text-sm">Rejected</span>
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
      :currentUser="currentUser"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import UploadModal from '@/Components/UploadModal.vue';
import SearchBar from '@/Components/SearchBar.vue';
import { useDeleteAlert } from '@/composables/useDeleteAlert.js';

const documents = ref([]);

const showUploadModal = ref(false);
const searchQuery = ref('');
const editIndex = ref(null);
const editFormData = ref(null);
const units = ref([]);
const canUpload = ref(false);
const { confirmDelete } = useDeleteAlert();

// Get current user from page props
const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

const columns = [
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
    const response = await fetch('/documents');
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

async function handleUpload(uploadData) {
  try {
    const formData = new FormData();
    
    // Add all form data
    for (const [key, value] of uploadData.entries()) {
      if (key === 'file') {
        formData.append('file', value);
      } else {
        formData.append(key, value);
      }
    }
    
    // Add document date if not present
    if (!formData.get('document_date')) {
      formData.append('document_date', new Date().toISOString().slice(0, 10));
    }
    
    const response = await fetch(route('documents.store'), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    
    if (response.ok) {
      const result = await response.json();
      if (result.success) {
        // Refresh the page or update the documents list
        window.location.reload();
      } else {
        alert('Error uploading document: ' + (result.message || 'Unknown error'));
      }
    } else {
      const errorData = await response.json();
      alert('Error uploading document: ' + (errorData.message || 'Unknown error'));
    }
  } catch (error) {
    console.error('Upload error:', error);
    alert('Error uploading document. Please try again.');
  }
  
  showUploadModal.value = false;
}



onMounted(() => {
  fetchUnits();
  checkCanUpload();
  fetchDocuments();
});
</script> 