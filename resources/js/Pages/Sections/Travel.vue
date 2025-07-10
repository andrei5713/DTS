<script setup>
import { ref, computed } from 'vue'
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';

const activeSection = ref('travel');
const showUploadModal = ref(false);
const editingIndex = ref(null);
const statusFilter = ref('All Status');
const searchQuery = ref('');

const travelDocuments = ref(JSON.parse(localStorage.getItem('travelDocuments')) || []);

const formData = ref({
  date: '',
  name: '',
  travelAuthorityNumber: '',
  destination: '',
  dateOfTravel: '',
  file: null,
  status: 'Pending'
});

const errors = ref({
  date: false,
  name: false,
  travelAuthorityNumber: false,
  destination: false,
  dateOfTravel: false,
  file: false
});

const confirmDeleteRef = ref(null);
const deleteIndex = ref(null);

const filteredDocuments = computed(() => {
  let filtered = travelDocuments.value;

  if (statusFilter.value !== 'All Status') {
    filtered = filtered.filter(doc => doc.status === statusFilter.value);
  }

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(doc =>
      doc.name?.toLowerCase().includes(query) ||
      doc.travelAuthorityNumber?.toLowerCase().includes(query) ||
      doc.destination?.toLowerCase().includes(query)
    );
  }

  return filtered;
});

function validateForm() {
  errors.value = {
    date: !formData.value.date,
    name: !formData.value.name,
    travelAuthorityNumber: !formData.value.travelAuthorityNumber,
    destination: !formData.value.destination,
    dateOfTravel: !formData.value.dateOfTravel,
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
    travelDocuments.value[editingIndex.value] = { ...formData.value };
  } else {
    travelDocuments.value.push({ ...formData.value });
  }

  localStorage.setItem('travelDocuments', JSON.stringify(travelDocuments.value));
  resetForm();
}

function editDocument(index) {
  editingIndex.value = index;
  formData.value = { ...travelDocuments.value[index] };
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
    travelDocuments.value.splice(deleteIndex.value, 1);
    localStorage.setItem('travelDocuments', JSON.stringify(travelDocuments.value));
    deleteIndex.value = null;
  }
}

function resetForm() {
  formData.value = {
    date: '',
    name: '',
    travelAuthorityNumber: '',
    destination: '',
    dateOfTravel: '',
    file: null,
    status: 'Pending'
  };
  errors.value = {
    date: false,
    name: false,
    travelAuthorityNumber: false,
    destination: false,
    dateOfTravel: false,
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
          <h1 class="text-2xl font-bold mb-2">Travel Orders</h1>
          <p class="mb-4">Manage travel-related documents and ensure proper authorization and tracking.</p>

          <!-- Inside the <template> section, replace the controls div with this: -->
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
    <select 
      v-model="statusFilter" 
      class="border border-gray-300 rounded px-4 py-2"
    >
      <option>All Status</option>
      <option>Pending</option>
      <option>Approved</option>
      <option>Completed</option>
    </select>
  </div>

  <div>
    <button
      @click="showUploadModal = true"
      class="border border-gray-400 rounded px-4 py-2 hover:bg-gray-100"
    >
      ✈️ Add Travel Order
    </button>
  </div>
</div>

          <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="border px-4 py-2 text-left">Date</th>
                  <th class="border px-4 py-2 text-left">Name</th>
                  <th class="border px-4 py-2 text-left">TA Number</th>
                  <th class="border px-4 py-2 text-left">Destination</th>
                  <th class="border px-4 py-2 text-left">Date of Travel</th>
                  <th class="border px-4 py-2 text-left">Status</th>
                  <th class="border px-4 py-2 text-left">File</th>
                  <th class="border px-4 py-2 text-left">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(doc, index) in filteredDocuments" :key="index">
                  <td class="border px-4 py-2">{{ doc.date }}</td>
                  <td class="border px-4 py-2">{{ doc.name }}</td>
                  <td class="border px-4 py-2">{{ doc.travelAuthorityNumber }}</td>
                  <td class="border px-4 py-2">{{ doc.destination }}</td>
                  <td class="border px-4 py-2">{{ doc.dateOfTravel }}</td>
                  <td class="border px-4 py-2">
                    <span :class="{
                      'text-yellow-600': doc.status === 'Pending',
                      'text-green-600': doc.status === 'Approved',
                      'text-blue-600': doc.status === 'Released'
                    }">{{ doc.status }}</span>
                  </td>
                  <td class="border px-4 py-2">
                    <a v-if="doc.file" :href="doc.file.dataUrl" :download="doc.file.name"
                      class="text-blue-600 hover:underline">
                      {{ doc.file.name }}
                    </a>
                    <span v-else>N/A</span>
                  </td>
                  <td class="border px-4 py-2">
                    <button @click="editDocument(index)" class="text-blue-600 hover:underline mr-2">Edit</button>
                    <button @click="deleteDocument(index)" class="text-red-600 hover:underline">Delete</button>
                  </td>
                </tr>
                <tr v-if="filteredDocuments.length === 0">
                  <td colspan="8" class="border px-4 py-4 text-center text-gray-500">No travel orders found</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

    <!-- Add Travel Order Modal -->
    <div v-if="showUploadModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="relative bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">

        <!-- Close Button -->
        <button @click="resetForm"
          class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl leading-none">
          &times;
        </button>

        <h2 class="text-2xl font-bold mb-4">
          {{ editingIndex !== null ? 'Edit Travel Order' : 'Add Travel Order' }}
        </h2>

        <form @submit.prevent="submitDocument" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-medium">Date *</label>
            <input v-model="formData.date" type="date" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.date }" />
            <p v-if="errors.date" class="text-red-500 text-xs mt-1">Date is required</p>
          </div>

          <div>
            <label class="block mb-1 font-medium">Name *</label>
            <input v-model="formData.name" type="text" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.name }" />
            <p v-if="errors.name" class="text-red-500 text-xs mt-1">Name is required</p>
          </div>

          <div>
            <label class="block mb-1 font-medium">Travel Authority Number *</label>
            <input v-model="formData.travelAuthorityNumber" type="text" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.travelAuthorityNumber }" />
            <p v-if="errors.travelAuthorityNumber" class="text-red-500 text-xs mt-1">TA Number is required</p>
          </div>

          <div>
            <label class="block mb-1 font-medium">Destination *</label>
            <input v-model="formData.destination" type="text" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.destination }" />
            <p v-if="errors.destination" class="text-red-500 text-xs mt-1">Destination is required</p>
          </div>

          <div>
            <label class="block mb-1 font-medium">Date of Travel *</label>
            <input v-model="formData.dateOfTravel" type="date" class="w-full border rounded px-3 py-2"
              :class="{ 'border-red-500': errors.dateOfTravel }" />
            <p v-if="errors.dateOfTravel" class="text-red-500 text-xs mt-1">Date of Travel is required</p>
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
