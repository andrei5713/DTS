<template>
  <div class="min-h-screen flex flex-col bg-gray-200">
    <Header />

    <div class="flex-grow p-4 mt-6">
      <Tabs v-model="activeTab">
        <template #default="{ activeTab }">

          <!-- Incoming Tab -->
          <div v-if="activeTab === 'incoming'">
             <h2 class="text-2xl font-bold mb-2">Incoming Documents</h2>
            <div class="flex flex-wrap items-center gap-4 mb-6">
              <input v-model="searchQuery" type="text" placeholder="Search subject, sender..."
                class="border border-gray-300 rounded px-3 py-2 text-sm flex-1 max-w-xs" />
              <select v-model="filterType" class="border rounded px-3 py-2 text-sm">
                <option value="">All Types</option>
                <option v-for="type in docTypes" :key="type" :value="type">{{ type }}</option>
              </select>
              <select v-model="filterStatus" class="border rounded px-3 py-2 text-sm">
                <option value="">All Status</option>
                <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
              </select>
            </div>

            <div class="overflow-auto border rounded">
              <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-left text-sm uppercase text-gray-600">
                  <tr>
                    <th class="px-4 py-2">Tracking No.</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Sender</th>
                    <th class="px-4 py-2">Priority</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Current Handler</th>
                    <th class="px-4 py-2">File</th>
                    <th class="px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="doc in filteredDocuments" :key="doc.id" class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono">{{ doc.tracking_number }}</td>
                    <td class="px-4 py-2">{{ doc.type }}</td>
                    <td class="px-4 py-2">{{ doc.subject }}</td>
                    <td class="px-4 py-2">{{ doc.date_of_entry }}</td>
                    <td class="px-4 py-2">{{ doc.sender }}</td>
                    <td class="px-4 py-2">
                      <span :class="priorityClasses(doc.priority)"
                        class="px-2 py-1 rounded text-xs font-medium capitalize">{{ doc.priority }}</span>
                    </td>
                    <td class="px-4 py-2">
                      <span :class="statusClasses(doc.status)"
                        class="px-2 py-1 rounded text-xs font-semibold capitalize">{{ doc.status }}</span>
                    </td>
                    <td class="px-4 py-2">{{ doc.current_handler }}</td>
                    <td class="px-4 py-2">
                      <a v-if="doc.file" :href="doc.file" target="_blank"
                        class="text-blue-600 hover:underline text-sm">View</a>
                    </td>
                    <td class="px-4 py-2">
                      <button class="text-blue-600 hover:underline text-sm" @click="viewHistory(doc)">History</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Outgoing Tab -->
          <div v-else-if="activeTab === 'outgoing'">
            <div class="flex items-center justify-between mb-2">
              <h2 class="text-2xl font-bold">Outgoing Documents</h2>
            </div>

            <div class="flex flex-wrap items-center gap-4 mb-6">
              <input v-model="searchQuery" type="text" placeholder="Search subject, recipient..."
                class="border rounded px-3 py-2 text-sm flex-1 max-w-xs" />
              <select v-model="filterType" class="border rounded px-3 py-2 text-sm">
                <option value="">All Types</option>
                <option v-for="type in docTypes" :key="type" :value="type">{{ type }}</option>
              </select>
              <select v-model="filterStatus" class="border rounded px-3 py-2 text-sm">
                <option value="">All Status</option>
                <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
              </select>
              <button @click="showModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-md text-sm shadow-md transition duration-200 ease-in-out">
                Upload Document
              </button>
            </div>

            <div class="overflow-auto border rounded">
              <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-left text-sm uppercase text-gray-600">
                  <tr>
                    <th class="px-4 py-2">Tracking No.</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2">Date Sent</th>
                    <th class="px-4 py-2">Recipient</th>
                    <th class="px-4 py-2">Priority</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Current Handler</th>
                    <th class="px-4 py-2">File</th>
                    <th class="px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="doc in filteredDocuments" :key="doc.id" class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono">{{ doc.tracking_number }}</td>
                    <td class="px-4 py-2">{{ doc.type }}</td>
                    <td class="px-4 py-2">{{ doc.subject }}</td>
                    <td class="px-4 py-2">{{ doc.date_of_entry }}</td>
                    <td class="px-4 py-2">{{ doc.sender }}</td>
                    <td class="px-4 py-2">
                      <span :class="priorityClasses(doc.priority)"
                        class="px-2 py-1 rounded text-xs font-medium capitalize">{{ doc.priority }}</span>
                    </td>
                    <td class="px-4 py-2">
                      <span :class="statusClasses(doc.status)"
                        class="px-2 py-1 rounded text-xs font-semibold capitalize">{{ doc.status }}</span>
                    </td>
                    <td class="px-4 py-2">{{ doc.current_handler }}</td>
                    <td class="px-4 py-2">
                      <a v-if="doc.file" :href="doc.file" target="_blank"
                        class="text-blue-600 hover:underline text-sm">View</a>
                    </td>
                    <td class="px-4 py-2">
                      <button class="text-green-600 hover:underline text-sm" @click="sendDocument(doc)">Send</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Archive Tab -->
          <div v-else-if="activeTab === 'archive'">
            <div class="flex items-center gap-4 mb-2">
              <h2 class="text-2xl font-bold">Archive Documents</h2>
              <h2 class="text-2x1 font-bold">—</h2>
              <select v-model="archiveType" class="rounded px-1 py-1 text-lg bg-white font-bold min-w-32">
                <option value="incoming">Incoming</option>
                <option value="outgoing">Outgoing</option>
              </select>
            </div>

            <div class="flex flex-wrap items-center gap-4 mb-6">
              <input v-model="searchQuery" type="text" :placeholder="archiveType === 'incoming' ? 'Search subject, sender...' : 'Search subject, recipient...'"
                class="border border-gray-300 rounded px-3 py-2 text-sm flex-1 max-w-xs" />
              <select v-model="filterType" class="border rounded px-3 py-2 text-sm">
                <option value="">All Types</option>
                <option v-for="type in docTypes" :key="type" :value="type">{{ type }}</option>
              </select>
              <select v-model="filterStatus" class="border rounded px-3 py-2 text-sm">
                <option value="">All Status</option>
                <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
              </select>
            </div>

            <!-- Archive Incoming Documents -->
            <div v-if="archiveType === 'incoming'" class="overflow-auto border rounded">
              <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-left text-sm uppercase text-gray-600">
                  <tr>
                    <th class="px-4 py-2">Tracking No.</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Sender</th>
                    <th class="px-4 py-2">Priority</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Current Handler</th>
                    <th class="px-4 py-2">File</th>
                    <th class="px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="doc in filteredArchivedDocuments" :key="doc.id" class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono">{{ doc.tracking_number }}</td>
                    <td class="px-4 py-2">{{ doc.type }}</td>
                    <td class="px-4 py-2">{{ doc.subject }}</td>
                    <td class="px-4 py-2">{{ doc.date_of_entry }}</td>
                    <td class="px-4 py-2">{{ doc.sender }}</td>
                    <td class="px-4 py-2">
                      <span :class="priorityClasses(doc.priority)"
                        class="px-2 py-1 rounded text-xs font-medium capitalize">{{ doc.priority }}</span>
                    </td>
                    <td class="px-4 py-2">
                      <span :class="statusClasses(doc.status)"
                        class="px-2 py-1 rounded text-xs font-semibold capitalize">{{ doc.status }}</span>
                    </td>
                    <td class="px-4 py-2">{{ doc.current_handler }}</td>
                    <td class="px-4 py-2">
                      <a v-if="doc.file" :href="doc.file" target="_blank"
                        class="text-blue-600 hover:underline text-sm">View</a>
                    </td>
                    <td class="px-4 py-2">
                      <button class="text-blue-600 hover:underline text-sm" @click="viewHistory(doc)">History</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Archive Outgoing Documents -->
            <div v-else-if="archiveType === 'outgoing'" class="overflow-auto border rounded">
              <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-left text-sm uppercase text-gray-600">
                  <tr>
                    <th class="px-4 py-2">Tracking No.</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2">Date Sent</th>
                    <th class="px-4 py-2">Recipient</th>
                    <th class="px-4 py-2">Priority</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Current Handler</th>
                    <th class="px-4 py-2">File</th>
                    <th class="px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="doc in filteredArchivedDocuments" :key="doc.id" class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono">{{ doc.tracking_number }}</td>
                    <td class="px-4 py-2">{{ doc.type }}</td>
                    <td class="px-4 py-2">{{ doc.subject }}</td>
                    <td class="px-4 py-2">{{ doc.date_of_entry }}</td>
                    <td class="px-4 py-2">{{ doc.sender }}</td>
                    <td class="px-4 py-2">
                      <span :class="priorityClasses(doc.priority)"
                        class="px-2 py-1 rounded text-xs font-medium capitalize">{{ doc.priority }}</span>
                    </td>
                    <td class="px-4 py-2">
                      <span :class="statusClasses(doc.status)"
                        class="px-2 py-1 rounded text-xs font-semibold capitalize">{{ doc.status }}</span>
                    </td>
                    <td class="px-4 py-2">{{ doc.current_handler }}</td>
                    <td class="px-4 py-2">
                      <a v-if="doc.file" :href="doc.file" target="_blank"
                        class="text-blue-600 hover:underline text-sm">View</a>
                    </td>
                    <td class="px-4 py-2">
                      <button class="text-gray-600 hover:underline text-sm" @click="restoreDocument(doc)">Restore</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>
      </Tabs>
    </div>

    <!-- Upload Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg space-y-4">
        <h2 class="text-lg font-bold">Upload Outgoing Document</h2>

        <select v-model="newDoc.type" class="w-full border rounded px-3 py-2 text-sm" required>
          <option value="" disabled>Select type of document</option>
          <option v-for="type in docTypes" :key="type" :value="type">{{ type }}</option>
        </select>

        <input v-model="newDoc.subject" placeholder="Subject" class="w-full border rounded px-3 py-2 text-sm" required />
        <input type="date" v-model="newDoc.date_of_entry" class="w-full border rounded px-3 py-2 text-sm" disabled />
        <input v-model="newDoc.sender" placeholder="Recipient" class="w-full border rounded px-3 py-2 text-sm" required />

        <select v-model="newDoc.priority" class="w-full border rounded px-3 py-2 text-sm" required>
          <option value="" disabled>Select priority level</option>
          <option value="Normal">Normal</option>
          <option value="Urgent">Urgent</option>
          <option value="Confidential">Confidential</option>
        </select>

        <input type="file" accept="application/pdf" @change="handleFileUpload" class="w-full" required />

        <div class="flex justify-end space-x-2 pt-4">
          <button @click="showModal = false" class="px-4 py-2 text-sm border rounded">Cancel</button>
          <button @click="submitDocument" class="bg-blue-500 text-white px-4 py-2 text-sm rounded hover:bg-blue-600">Submit</button>
        </div>
      </div>
    </div>

    <footer class="text-center text-sm text-gray-500 mt-auto py-4">
      &copy; 2025 National Food Authority - Quezon City Branch. All Rights Reserved.
    </footer>
  </div>
</template>

<script setup>
import Header from '../Components/Header.vue'
import Tabs from '../Components/Tabs.vue'
import { ref, computed } from 'vue'

const activeTab = ref('incoming')
const searchQuery = ref('')
const filterType = ref('')
const filterStatus = ref('')
const showModal = ref(false)
const archiveType = ref('incoming')

const docTypes = ['Memo', 'Letter', 'PR', 'DV']
const statusOptions = ['Pending', 'Received', 'For Signature', 'Completed', 'Returned']

const documents = ref([
  {
    id: 1,
    tracking_number: 'CPMSD-2025-07-001',
    type: 'Memo',
    subject: 'Budget Request for Q3',
    date_of_entry: '2025-07-20',
    sender: 'Finance Division',
    priority: 'Urgent',
    status: 'Pending',
    current_handler: 'Office of the Director',
    file: ''
  }
])

// Sample archived documents
const archivedDocuments = ref([
  {
    id: 101,
    tracking_number: 'CPMSD-2025-06-015',
    type: 'Letter',
    subject: 'Annual Report Submission',
    date_of_entry: '2025-06-15',
    sender: 'Regional Office',
    priority: 'Normal',
    status: 'Completed',
    current_handler: 'Records Section',
    file: '',
    archived_type: 'incoming'
  },
  {
    id: 102,
    tracking_number: 'CPMSD-2025-06-022',
    type: 'PR',
    subject: 'Equipment Purchase Request',
    date_of_entry: '2025-06-22',
    sender: 'IT Department',
    priority: 'Confidential',
    status: 'Completed',
    current_handler: 'Procurement',
    file: '',
    archived_type: 'outgoing'
  }
])

const today = new Date().toISOString().split('T')[0]

const newDoc = ref({
  type: '',
  subject: '',
  date_of_entry: today,
  sender: '',
  priority: '',
  file: ''
})

const generateTrackingNumber = () => {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const count = documents.value.length + 1
  return `CPMSD-${year}-${month}-${String(count).padStart(3, '0')}`
}

const handleFileUpload = (e) => {
  const file = e.target.files[0]
  if (file && file.type === 'application/pdf') {
    newDoc.value.file = URL.createObjectURL(file)
  } else {
    alert('Only PDF files are allowed.')
    e.target.value = null
  }
}

const submitDocument = () => {
  // Validation - check if all required fields are filled
  if (!newDoc.value.type) {
    alert('Please select a document type.')
    return
  }
  
  if (!newDoc.value.subject.trim()) {
    alert('Please enter a subject.')
    return
  }
  
  if (!newDoc.value.sender.trim()) {
    alert('Please enter a recipient.')
    return
  }
  
  if (!newDoc.value.priority) {
    alert('Please select a priority level.')
    return
  }
  
  if (!newDoc.value.file) {
    alert('Please upload a PDF file.')
    return
  }

  const autoTracking = generateTrackingNumber()

  documents.value.push({
    ...newDoc.value,
    id: documents.value.length + 1,
    tracking_number: autoTracking,
    status: 'Pending',
    current_handler: ''
  })

  newDoc.value = {
    type: '',
    subject: '',
    date_of_entry: today,
    sender: '',
    priority: '',
    file: ''
  }

  showModal.value = false
}

const filteredDocuments = computed(() =>
  documents.value.filter(doc =>
    (filterType.value === '' || doc.type === filterType.value) &&
    (filterStatus.value === '' || doc.status === filterStatus.value) &&
    (doc.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      doc.sender.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
)

const filteredArchivedDocuments = computed(() =>
  archivedDocuments.value.filter(doc =>
    doc.archived_type === archiveType.value &&
    (filterType.value === '' || doc.type === filterType.value) &&
    (filterStatus.value === '' || doc.status === filterStatus.value) &&
    (doc.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      doc.sender.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
)

function viewHistory(doc) {
  alert(`Viewing history for ${doc.tracking_number}`)
}

function sendDocument(doc) {
  alert(`Sending document: ${doc.tracking_number}`)
}

function restoreDocument(doc) {
  // Move document back to active documents
  const restoredDoc = { ...doc }
  delete restoredDoc.archived_type
  
  documents.value.push(restoredDoc)
  
  // Remove from archived documents
  const index = archivedDocuments.value.findIndex(d => d.id === doc.id)
  if (index > -1) {
    archivedDocuments.value.splice(index, 1)
  }
  
  alert(`Document ${doc.tracking_number} has been restored`)
}

function priorityClasses(priority) {
  return {
    Normal: 'bg-gray-200 text-gray-700',
    Urgent: 'bg-yellow-200 text-yellow-800',
    Confidential: 'bg-red-200 text-red-800'
  }[priority] || 'bg-gray-100 text-gray-600'
}

function statusClasses(status) {
  return {
    Pending: 'bg-gray-200 text-gray-800',
    Received: 'bg-blue-200 text-blue-800',
    'For Signature': 'bg-indigo-200 text-indigo-800',
    Completed: 'bg-green-200 text-green-800',
    Returned: 'bg-red-200 text-red-800'
  }[status] || 'bg-gray-100 text-gray-600'
}
</script>

<style scoped>
/* No extra styles */
</style>