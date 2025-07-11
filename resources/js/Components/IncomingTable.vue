<script setup>
import { ref, computed } from 'vue';
import { FilePlus } from 'lucide-vue-next';
import Swal from 'sweetalert2';

const searchQuery = ref('');
const selectedType = ref('');

const documents = ref([
  {
    status: 'Complex',
    date: '2025-07-04',
    department: 'Finance',
    code: 'F123',
    subject: 'Budget Report',
    description: 'Quarterly update',
    receivedBy: 'John A.',
    dateReceived: '2025-07-03',
    timeReceived: '09:00',
    type: 'BUR',
    file: 'budget.pdf',
    fileUrl: '',
  },
]);

const filteredDocuments = computed(() => {
  return documents.value.filter(doc => {
    const matchesSearch =
      searchQuery.value === '' ||
      doc.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      doc.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      doc.department.toLowerCase().includes(searchQuery.value.toLowerCase());

    const matchesType =
      selectedType.value === '' || doc.type === selectedType.value;

    return matchesSearch && matchesType;
  });
});

const showUploadModal = ref(false);
const todayDate = new Date().toISOString().split('T')[0];

const uploadForm = ref({
  status: '',
  date: todayDate,
  department: '',
  code: '',
  subject: '',
  description: '',
  receivedBy: '',
  dateReceived: '',
  timeReceived: '',
  type: '',
  file: null,
  fileUrl: null,
});

const uploadFormIsValid = computed(() => {
  const f = uploadForm.value;
  return (
    f.status &&
    f.date &&
    f.department &&
    f.code &&
    f.subject &&
    f.description &&
    f.receivedBy &&
    f.dateReceived &&
    f.type &&
    f.file
  );
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
    status: '',
    date: todayDate,
    department: '',
    code: '',
    subject: '',
    description: '',
    receivedBy: '',
    dateReceived: '',
    timeReceived: '',
    type: '',
    file: null,
    fileUrl: null,
  };
}

function submitUploadForm() {
  if (!uploadFormIsValid.value) {
    Swal.fire('Error', 'Please fill in all required fields and select a valid PDF.', 'error');
    return;
  }

  const fileUrl = URL.createObjectURL(uploadForm.value.file);

  documents.value.unshift({
    status: uploadForm.value.status,
    date: uploadForm.value.date,
    department: uploadForm.value.department,
    code: uploadForm.value.code,
    subject: uploadForm.value.subject,
    description: uploadForm.value.description,
    receivedBy: uploadForm.value.receivedBy,
    dateReceived: uploadForm.value.dateReceived,
    timeReceived: uploadForm.value.timeReceived,
    type: uploadForm.value.type,
    file: uploadForm.value.file.name,
    fileUrl,
  });

  Swal.fire({
    icon: 'success',
    title: 'Document uploaded!',
    toast: true,
    position: 'top-end',
    timer: 1500,
    showConfirmButton: false,
  });
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
                  <select v-model="uploadForm.status" class="border rounded px-2 py-1 flex-1" required>
                    <option value="">Select Status</option>
                    <option value="Complex">Complex</option>
                    <option value="Simple">Simple</option>
                    <option value="Highly Technical">Highly Technical</option>
                  </select>
                  <input v-model="uploadForm.date" type="date" class="border rounded px-2 py-1 flex-1" required />
                </div>
                <div class="flex gap-2">
                  <select v-model="uploadForm.department" class="border rounded px-2 py-1 flex-1" required>
                    <option value="">Select Department</option>
                    <option value="DA">DA</option>
                    <option value="Finance">Finance</option>
                  </select>
                  <input v-model="uploadForm.code" type="text" placeholder="Code" class="border rounded px-2 py-1 flex-1" required />
                </div>
                <div class="flex gap-2">
                  <input v-model="uploadForm.subject" type="text" placeholder="Subject" class="border rounded px-2 py-1 flex-1" required />
                  <input v-model="uploadForm.receivedBy" type="text" placeholder="Received By" class="border rounded px-2 py-1 flex-1" required />
                </div>
                <textarea v-model="uploadForm.description" placeholder="Short Description" class="border rounded px-2 py-1 w-full" required></textarea>
                <div class="flex gap-2">
                  <input v-model="uploadForm.dateReceived" type="date" class="border rounded px-2 py-1 flex-1" required />
                  <input v-model="uploadForm.timeReceived" type="time" class="border rounded px-2 py-1 flex-1" />
                </div>
                <select v-model="uploadForm.type" class="border rounded px-2 py-1 w-full" required>
                  <option value="">Select Type</option>
                  <option value="Travel">Travel</option>
                  <option value="BUR">BUR</option>
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
          <table class="bg-white border border-gray-300 text-sm w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Status</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Date</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Department</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Code</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Subject</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Description</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Received By</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Date Received</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Time Received</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">File</th>
                <th class="border px-4 py-2 text-left align-top whitespace-normal">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(doc, index) in filteredDocuments" :key="index">
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <select v-model="doc.status" class="w-full bg-transparent">
                    <option value="Complex">Complex</option>
                    <option value="Simple">Simple</option>
                    <option value="Highly Technical">Highly Technical</option>
                  </select>
                </td>
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <input v-model="doc.date" type="date" class="w-full bg-transparent" />
                </td>
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <select v-model="doc.department" class="w-full bg-transparent">
                    <option value="DA">DA</option>
                    <option value="Finance">Finance</option>
                  </select>
                </td>
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <input v-model="doc.code" class="w-full bg-transparent" />
                </td>
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <input v-model="doc.subject" class="w-full bg-transparent" />
                </td>
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <input v-model="doc.description" class="w-full bg-transparent" />
                </td>
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <input v-model="doc.receivedBy" class="w-full bg-transparent" />
                </td>
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <input v-model="doc.dateReceived" type="date" class="w-full bg-transparent" />
                </td>
                <td class="border px-4 py-2 align-top whitespace-normal">
                  <input v-model="doc.timeReceived" type="time" class="w-full bg-transparent" />
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
          </table>
            <!-- No Documents Found Message -->
  <div v-if="filteredDocuments.length === 0" class="text-center py-10 text-gray-500">
    No documents found.
  </div>
</template>