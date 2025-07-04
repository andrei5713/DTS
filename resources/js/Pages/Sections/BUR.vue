<script setup>
import { ref, computed } from 'vue'
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';

const activeSection = ref('bur');
const showUploadModal = ref(false);

// Documents list, initialized from localStorage
const documents = ref([]);
const storedDocs = localStorage.getItem('documents');
documents.value = storedDocs ? JSON.parse(storedDocs) : [];

// Search query
const searchQuery = ref('');

// Computed filtered documents
const filteredDocuments = computed(() => {
  if (!searchQuery.value.trim()) {
    return documents.value;
  }
  const query = searchQuery.value.toLowerCase();
  return documents.value.filter(doc =>
    doc.payee?.toLowerCase().includes(query) ||
    doc.particulars?.toLowerCase().includes(query) ||
    doc.amount?.toString().includes(query) ||
    doc.dateReleased?.toLowerCase().includes(query) ||
    doc.chequeDate?.toLowerCase().includes(query) ||
    doc.file?.name?.toLowerCase().includes(query)
  );
});

// Form data
const formData = ref({
  payee: '',
  particulars: '',
  amount: '',
  dateReleased: '',
  chequeDate: '',
  file: null
});

function handleSectionChange(newSection) {
  activeSection.value = newSection;
}

function handleFileChange(e) {
  formData.value.file = e.target.files[0];
}

function submitDocument() {
  documents.value.push({ ...formData.value });
  // Save to localStorage
  localStorage.setItem('documents', JSON.stringify(documents.value));

  showUploadModal.value = false;

  formData.value = {
    payee: '',
    particulars: '',
    amount: '',
    dateReleased: '',
    chequeDate: '',
    file: null
  };
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    <Header />

    <!-- Sidebar + Main -->
    <div class="flex flex-1 p-6">
      <!-- Sidebar -->
      <Sidebar :active-section="activeSection" @section-change="handleSectionChange" />

      <!-- Main Content -->
      <main class="flex-1 ml-6 overflow-auto">

        <!-- BUR Controls -->
        <div class="bg-white p-6 rounded shadow mt-6">
          <h1 class="text-2xl font-bold mb-2">BUR</h1>
          <p class="mb-4">Manage budget utilization requests and ensure proper documentation and approval.</p>

          <!-- Controls Row -->
          <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-4 md:space-y-0 mb-4">
            <!-- Search Input -->
            <div class="flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search Documents..."
                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300"
              />
            </div>

            <!-- Status Dropdown -->
            <div>
              <select
                class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300"
              >
                <option>All Status</option>
                <option>Pending</option>
                <option>Approved</option>
                <option>Released</option>
              </select>
            </div>

            <!-- Upload Button -->
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
                  <th class="border px-4 py-2 text-left text-sm font-medium text-gray-700">Payee</th>
                  <th class="border px-4 py-2 text-left text-sm font-medium text-gray-700">Particulars</th>
                  <th class="border px-4 py-2 text-left text-sm font-medium text-gray-700">Amount</th>
                  <th class="border px-4 py-2 text-left text-sm font-medium text-gray-700">Date Released</th>
                  <th class="border px-4 py-2 text-left text-sm font-medium text-gray-700">Cheque Date</th>
                  <th class="border px-4 py-2 text-left text-sm font-medium text-gray-700">File Document</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(doc, index) in filteredDocuments" :key="index">
                  <td class="border px-4 py-2 text-sm text-gray-600">{{ doc.payee }}</td>
                  <td class="border px-4 py-2 text-sm text-gray-600">{{ doc.particulars }}</td>
                  <td class="border px-4 py-2 text-sm text-gray-600">₱{{ doc.amount }}</td>
                  <td class="border px-4 py-2 text-sm text-gray-600">{{ doc.dateReleased }}</td>
                  <td class="border px-4 py-2 text-sm text-gray-600">{{ doc.chequeDate }}</td>
                  <td class="border px-4 py-2 text-sm text-blue-600 underline cursor-pointer">
                    {{ doc.file ? doc.file.name : 'N/A' }}
                  </td>
                </tr>
                <tr v-if="filteredDocuments.length === 0">
                  <td colspan="6" class="border px-4 py-4 text-center text-gray-500">
                    No documents found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

    <!-- Upload Document Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[80vh] overflow-y-auto relative">
        <!-- Close Button -->
        <button
          @click="showUploadModal = false"
          aria-label="Close modal"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl font-bold"
        >
          &times;
        </button>

        <h2 class="text-xl font-bold mb-4">Add Document</h2>

        <!-- BUR Information -->
        <div class="mb-6">
          <h3 class="font-semibold mb-2">BUR Information</h3>
          <div class="space-y-4">
            <!-- Payee -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Payee</label>
              <input 
                v-model="formData.payee"
                type="text" 
                placeholder="Payee"
                class="w-full border border-gray-300 rounded px-3 py-2"
              >
            </div>

            <!-- Particulars -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Particulars</label>
              <input 
                v-model="formData.particulars"
                type="text" 
                placeholder="Particulars"
                class="w-full border border-gray-300 rounded px-3 py-2"
              >
            </div>

            <!-- Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Amount</label>
              <input 
                v-model="formData.amount"
                type="number" 
                placeholder="₱"
                class="w-full border border-gray-300 rounded px-3 py-2"
              >
            </div>
          </div>
        </div>

        <!-- File Attachment -->
        <div class="mb-6">
          <h3 class="font-semibold mb-2">File Attachment</h3>
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Choose File</label>
            <input 
              type="file" 
              @change="handleFileChange"
              class="mt-1 block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100"
            >
          </div>
        </div>

        <!-- Dates -->
        <div class="mb-6">
          <h3 class="font-semibold mb-2">Dates</h3>
          <div class="space-y-4">
            <!-- Date Released -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Date Released</label>
              <input 
                v-model="formData.dateReleased"
                type="date" 
                class="w-full border border-gray-300 rounded px-3 py-2"
              >
            </div>

            <!-- Cheque Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Cheque Date</label>
              <input 
                v-model="formData.chequeDate"
                type="date" 
                class="w-full border border-gray-300 rounded px-3 py-2"
              >
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button 
            @click="submitDocument"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Submit Document
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
