<script setup>
import { ref, computed } from 'vue'
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';

const activeSection = ref('bur');
const showUploadModal = ref(false);
const editingIndex = ref(null);
const statusFilter = ref('All Status');
const searchQuery = ref('');

// Documents list from localStorage
const documents = ref(JSON.parse(localStorage.getItem('documents')) || []);

// Form data with validation
const formData = ref({
  payee: '',
  particulars: '',
  amount: '',
  dateReleased: '',
  chequeDate: '',
  file: null,
  status: 'Pending'
});

const errors = ref({
  payee: false,
  particulars: false,
  amount: false,
  dateReleased: false,
  file: false
});

const confirmDeleteRef = ref(null);
const deleteIndex = ref(null);

// Computed filtered documents
const filteredDocuments = computed(() => {
  let filtered = documents.value;

  // Status filter
  if (statusFilter.value !== 'All Status') {
    filtered = filtered.filter(doc => doc.status === statusFilter.value);
  }

  // Search filter
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(doc =>
      doc.payee?.toLowerCase().includes(query) ||
      doc.particulars?.toLowerCase().includes(query) ||
      doc.amount?.toString().includes(query) ||
      doc.dateReleased?.toLowerCase().includes(query) ||
      doc.chequeDate?.toLowerCase().includes(query)
    );
  }

  return filtered;
});

function validateForm() {
  errors.value = {
    payee: !formData.value.payee,
    particulars: !formData.value.particulars,
    amount: !formData.value.amount || isNaN(formData.value.amount),
    dateReleased: !formData.value.dateReleased,
    file: !formData.value.file
  };

  return !Object.values(errors.value).some(error => error);
}

function handleFileChange(e) {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('File size exceeds 5MB limit');
      return;
    }
    const reader = new FileReader();
    reader.onload = () => {
      formData.value.file = {
        name: file.name,
        dataUrl: reader.result
      };
      errors.value.file = false;
    };
    reader.readAsDataURL(file);
  }
}

function submitDocument() {
  if (!validateForm()) return;

  if (editingIndex.value !== null) {
    documents.value[editingIndex.value] = { ...formData.value };
  } else {
    documents.value.push({ ...formData.value });
  }

  localStorage.setItem('documents', JSON.stringify(documents.value));
  resetForm();
}

function editDocument(index) {
  editingIndex.value = index;
  formData.value = { ...documents.value[index] };
  showUploadModal.value = true;
}

function deleteDocument(index) {
  deleteIndex.value = index;
  if (confirmDeleteRef.value && confirmDeleteRef.value.show) {
    confirmDeleteRef.value.show();
  }
}

function handleConfirmDelete() {
  if (deleteIndex.value !== null) {
    documents.value.splice(deleteIndex.value, 1);
    localStorage.setItem('documents', JSON.stringify(documents.value));
    deleteIndex.value = null;
  }
}

function resetForm() {
  formData.value = {
    payee: '',
    particulars: '',
    amount: '',
    dateReleased: '',
    chequeDate: '',
    file: null,
    status: 'Pending'
  };
  errors.value = {
    payee: false,
    particulars: false,
    amount: false,
    dateReleased: false,
    file: false
  };
  editingIndex.value = null;
  showUploadModal.value = false;
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Header />
    <div class="flex flex-1 p-6">
      <Sidebar :active-section="activeSection" />

      <main class="flex-1 ml-6 overflow-auto">
        <div class="bg-white p-6 rounded shadow mt-6">
          <h1 class="text-2xl font-bold mb-2">BUR</h1>
          <p class="mb-4">Manage budget utilization requests</p>

          <!-- Controls -->
          <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-4 md:space-y-0 mb-4">
            <div class="flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search..."
                class="w-64 border border-gray-300 rounded px-4 py-2"
              />
            </div>

            <div>
              <select v-model="statusFilter" class="border border-gray-300 rounded px-4 py-2">
                <option>All Status</option>
                <option>Pending</option>
                <option>Approved</option>
                <option>Released</option>
              </select>
            </div>

            <div>
              <button
                @click="showUploadModal = true"
                class="border border-gray-400 rounded px-4 py-2 hover:bg-gray-100"
              >
                📁 Upload Document
              </button>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="border px-4 py-2 text-left">Payee</th>
                  <th class="border px-4 py-2 text-left">Particulars</th>
                  <th class="border px-4 py-2 text-left">Amount</th>
                  <th class="border px-4 py-2 text-left">Date Released</th>
                  <th class="border px-4 py-2 text-left">Cheque Date</th>
                  <th class="border px-4 py-2 text-left">Status</th>
                  <th class="border px-4 py-2 text-left">File</th>
                  <th class="border px-4 py-2 text-left">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(doc, index) in filteredDocuments" :key="index">
                  <td class="border px-4 py-2">{{ doc.payee }}</td>
                  <td class="border px-4 py-2">{{ doc.particulars }}</td>
                  <td class="border px-4 py-2">₱{{ doc.amount }}</td>
                  <td class="border px-4 py-2">{{ doc.dateReleased }}</td>
                  <td class="border px-4 py-2">{{ doc.chequeDate }}</td>
                  <td class="border px-4 py-2">
                    <span :class="{
                      'text-yellow-600': doc.status === 'Pending',
                      'text-green-600': doc.status === 'Approved',
                      'text-blue-600': doc.status === 'Released'
                    }">
                      {{ doc.status }}
                    </span>
                  </td>
                  <td class="border px-4 py-2">
                    <a v-if="doc.file"
                       :href="doc.file.dataUrl"
                       :download="doc.file.name"
                       class="text-blue-600 hover:underline">
                      {{ doc.file.name }}
                    </a>
                    <span v-else>N/A</span>
                  </td>
                  <td class="border px-4 py-2">
                    <button @click="editDocument(index)" class="text-blue-600 hover:underline mr-2">
                      Edit
                    </button>
                    <button @click="deleteDocument(index)" class="text-red-600 hover:underline">
                      Delete
                    </button>
                  </td>
                </tr>
                <tr v-if="filteredDocuments.length === 0">
                  <td colspan="8" class="border px-4 py-4 text-center text-gray-500">
                    No documents found
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

    <!-- New Add Document Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-4">
          {{ editingIndex !== null ? 'Edit Document' : 'Add Document' }}
        </h2>

        <form @submit.prevent="submitDocument" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-medium">Payee *</label>
            <input v-model="formData.payee" type="text" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.payee }" />
            <p v-if="errors.payee" class="text-red-500 text-xs mt-1">Payee is required</p>
          </div>

          <div>
            <label class="block mb-1 font-medium">Particulars *</label>
            <input v-model="formData.particulars" type="text" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.particulars }" />
            <p v-if="errors.particulars" class="text-red-500 text-xs mt-1">Particulars are required</p>
          </div>

          <div>
            <label class="block mb-1 font-medium">Amount (₱) *</label>
            <input v-model="formData.amount" type="number" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.amount }" />
            <p v-if="errors.amount" class="text-red-500 text-xs mt-1">Valid amount is required</p>
          </div>

          <div>
            <label class="block mb-1 font-medium">Date Released *</label>
            <input v-model="formData.dateReleased" type="date" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.dateReleased }" />
            <p v-if="errors.dateReleased" class="text-red-500 text-xs mt-1">Date is required</p>
          </div>

          <div>
            <label class="block mb-1 font-medium">Cheque Date</label>
            <input v-model="formData.chequeDate" type="date" class="w-full border rounded px-3 py-2" />
          </div>

          <div>
            <label class="block mb-1 font-medium">Status</label>
            <select v-model="formData.status" class="w-full border rounded px-3 py-2">
              <option>Pending</option>
              <option>Approved</option>
              <option>Released</option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="block mb-1 font-medium">File *</label>
            <input type="file" @change="handleFileChange" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.file }" />
            <p v-if="errors.file" class="text-red-500 text-xs mt-1">File is required</p>
            <p v-if="formData.file" class="text-sm mt-1">Selected: {{ formData.file.name }}</p>
          </div>
        </form>

        <div class="flex justify-end space-x-3 mt-6">
          <button @click="resetForm" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
          <button @click="submitDocument" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ editingIndex !== null ? 'Update' : 'Submit' }}
          </button>
        </div>
      </div>
    </div>

    <ConfirmDelete ref="confirmDeleteRef" :onConfirm="handleConfirmDelete" />
  </div>
</template>
