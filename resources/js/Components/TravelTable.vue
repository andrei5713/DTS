<script setup>
import { ref, computed } from 'vue';
import { FilePlus } from 'lucide-vue-next';
import Swal from 'sweetalert2';

const searchQuery = ref('');
const selectedType = ref('');

const documents = ref([
  {
    date: '2025-07-09',
    code: 'TA-003',
    subject: 'Jose Ramirez',
    department: 'Baguio City',
    dateReceived: '2025-07-14',
    file: 'jose-travel.pdf',
    fileUrl: '/travel',
    type: 'Travel',
  },
]);

const filteredDocuments = computed(() => {
  return documents.value.filter(doc => {
    const matchesSearch =
      searchQuery.value === '' ||
      doc.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (doc.description || '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      doc.department.toLowerCase().includes(searchQuery.value.toLowerCase());

    const matchesType =
      selectedType.value === '' || doc.type === selectedType.value;

    return matchesSearch && matchesType;
  });
});

const showUploadModal = ref(false);
const todayDate = new Date().toISOString().split('T')[0];

const uploadForm = ref({
  date: todayDate,
  code: '',
  subject: '',
  department: '',
  dateReceived: '',
  type: 'Travel',
  file: null,
  fileUrl: null,
});

function handleUpload() {
  showUploadModal.value = true;
}

function closeUploadModal() {
  if (uploadForm.value.fileUrl) {
    URL.revokeObjectURL(uploadForm.value.fileUrl);
  }

  showUploadModal.value = false;
  uploadForm.value = {
    date: todayDate,
    code: '',
    subject: '',
    department: '',
    dateReceived: '',
    type: 'Travel',
    file: null,
    fileUrl: null,
  };
}

function submitUploadForm() {
  if (!uploadForm.value.file || uploadForm.value.file.type !== 'application/pdf') {
    Swal.fire('Error', 'Please upload a valid PDF file.', 'error');
    return;
  }

  const fileUrl = URL.createObjectURL(uploadForm.value.file);
  documents.value.unshift({
    date: uploadForm.value.date,
    code: uploadForm.value.code,
    subject: uploadForm.value.subject,
    department: uploadForm.value.department,
    dateReceived: uploadForm.value.dateReceived,
    type: uploadForm.value.type,
    file: uploadForm.value.file.name,
    fileUrl,
  });

  Swal.fire('Success', 'Document uploaded successfully!', 'success');
  closeUploadModal();
}

function handleFileChange(e) {
  const file = e.target.files[0];
  if (file && file.type === 'application/pdf') {
    if (uploadForm.value.fileUrl) {
      URL.revokeObjectURL(uploadForm.value.fileUrl);
    }
    uploadForm.value.file = file;
    uploadForm.value.fileUrl = URL.createObjectURL(file);
  } else {
    uploadForm.value.file = null;
    uploadForm.value.fileUrl = null;
    e.target.value = '';
    Swal.fire('Error', 'Only PDF files are allowed.', 'error');
  }
}

function deleteDocument(index) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the document permanently.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      const fileUrl = documents.value[index].fileUrl;
      if (fileUrl) URL.revokeObjectURL(fileUrl);
      documents.value.splice(index, 1);
      Swal.fire('Deleted!', 'Document has been removed.', 'success');
    }
  });
}
</script>
<template>
           <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-4 w-full">
            <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full lg:w-auto">
              <input v-model="searchQuery" type="text" placeholder="Search Documents..." class="border border-gray-300 rounded px-3 py-2 w-full sm:w-[300px] md:w-[400px] focus:outline-none focus:ring focus:border-blue-300" />
              <select v-model="selectedType" class="border border-gray-300 rounded px-2 py-2 w-full sm:w-[150px] focus:outline-none focus:ring focus:border-blue-300">
                <option value="">All types</option>
                <option value="Travel">Travel</option>
                <option value="BUR">BUR</option>
              </select>
            </div>

            <button @click="handleUpload" class="flex justify-center items-center gap-2 border-2 border-dashed border-gray-400 text-gray-700 bg-gray-100 px-4 py-2 rounded hover:bg-gray-200 w-full sm:w-auto">
              <FilePlus class="w-5 h-5" />
              <span class="truncate">Upload Document</span>
            </button>
          </div>

          <!-- Upload Modal -->
          <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
              <h2 class="text-xl font-bold mb-4">Upload Document</h2>
<form @submit.prevent="submitUploadForm" class="space-y-3">
  <div class="flex gap-2">
    <input v-model="uploadForm.date" type="date" class="border rounded px-2 py-1 flex-1" required />
    <input v-model="uploadForm.code" type="text" placeholder="Travel Authority Number" class="border rounded px-2 py-1 flex-1" required />
  </div>
  <div class="flex gap-2">
    <input v-model="uploadForm.subject" type="text" placeholder="Name" class="border rounded px-2 py-1 flex-1" required />
    <input v-model="uploadForm.department" type="text" placeholder="Destination" class="border rounded px-2 py-1 flex-1" required />
  </div>
  <input v-model="uploadForm.dateReceived" type="date" class="border rounded px-2 py-1 w-full" required />
  <select v-model="uploadForm.type" class="border rounded px-2 py-1 w-full" required>
    <option value="">Select Type</option>
    <option value="Travel">Travel</option>
  </select>
  <input type="file" @change="handleFileChange" class="border rounded px-2 py-1 w-full" required />
  <div class="flex justify-between items-center mt-4">
    <button @click="closeUploadModal" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
  </div>
</form>
            </div>
          </div>

          <!-- Table -->
<!-- Table -->
<table class="bg-white border border-gray-300 text-sm w-full">
  <thead class="bg-gray-100">
    <tr>
      <th class="border px-4 py-2 text-left align-top whitespace-normal">Date</th>
      <th class="border px-4 py-2 text-left align-top whitespace-normal">Travel Authority Number</th>
      <th class="border px-4 py-2 text-left align-top whitespace-normal">Name</th>
      <th class="border px-4 py-2 text-left align-top whitespace-normal">Destination</th>
      <th class="border px-4 py-2 text-left align-top whitespace-normal">Date of Travel</th>
      <th class="border px-4 py-2 text-left align-top whitespace-normal">File Document</th>
      <th class="border px-4 py-2 text-left align-top whitespace-normal">Action</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="(doc, index) in filteredDocuments" :key="index">
      <td class="border px-4 py-2 align-top whitespace-normal">
        <input v-model="doc.date" type="date" class="w-full bg-transparent" />
      </td>
      <td class="border px-4 py-2 align-top whitespace-normal">
        <input v-model="doc.code" class="w-full bg-transparent" />
      </td>
      <td class="border px-4 py-2 align-top whitespace-normal">
        <input v-model="doc.subject" class="w-full bg-transparent" />
      </td>
      <td class="border px-4 py-2 align-top whitespace-normal">
        <input v-model="doc.department" class="w-full bg-transparent" />
      </td>
      <td class="border px-4 py-2 align-top whitespace-normal">
        <input v-model="doc.dateReceived" type="date" class="w-full bg-transparent" />
      </td>
      <td class="border px-4 py-2 align-top whitespace-normal">
        <a v-if="doc.fileUrl" :href="doc.fileUrl" target="_blank" class="text-blue-500 underline">{{ doc.file }}</a>
        <span v-else class="text-gray-400">No file</span>
      </td>
      <td class="border px-4 py-2 align-top whitespace-normal">
        <button @click="deleteDocument(index)" class="text-red-500">Delete</button>
      </td>
    </tr>
  </tbody>
    <!-- No Documents Found Message -->
</table>   
<div v-if="filteredDocuments.length === 0" class="text-center py-5 text-gray-500">
  No documents found.
</div>
</template>