<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
import SearchBar from '@/Components/SearchBar.vue';
import DepartmentFilter from '@/Components/DepartmentFilter.vue';
import UploadModal from '@/Components/UploadModal.vue';
import { useDocuments } from '@/composables/useDocuments.js';
import { useDeleteAlert } from '@/composables/useDeleteAlert.js'

const activeSection = ref('outgoing');
const searchQuery = ref('');
const selectedDepartment = ref('');
const showUploadModal = ref(false);
const actionMenuOpen = ref({});

const { documents, addDocument } = useDocuments();
const { confirmDelete } = useDeleteAlert();

// Computed property to filter documents based on search query and department
const filteredDocuments = computed(() => {
    let filtered = documents.value;
    
    // Filter by department if selected
    if (selectedDepartment.value) {
        filtered = filtered.filter(doc => 
            doc.deptId === selectedDepartment.value || 
            doc.department === selectedDepartment.value
        );
    }
    
    // Filter by search query if provided
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim();
        filtered = filtered.filter(doc => 
            doc.trackingCode?.toLowerCase().includes(query) ||
            doc.trackingNo?.toLowerCase().includes(query) ||
            doc.subject?.toLowerCase().includes(query) ||
            doc.shortDescription?.toLowerCase().includes(query) ||
            doc.deptId?.toLowerCase().includes(query) ||
            doc.department?.toLowerCase().includes(query) ||
            doc.code?.toLowerCase().includes(query) ||
            doc.receivedBy?.toLowerCase().includes(query) ||
            doc.dateReceived?.toLowerCase().includes(query) ||
            doc.timeReceived?.toLowerCase().includes(query)
        );
    }
    
    return filtered;
});

function handleSectionChange(newSection) {
    activeSection.value = newSection;
}

async function fetchDocuments() {
    try {
        const res = await fetch('/api/documents?type=outgoing');
        documents.value = await res.json();
    } catch (e) {
        console.error('Failed to fetch documents', e);
    }
}

function handleUpload() {
    showUploadModal.value = true;
}

function handleUploadSubmit(uploadData) {
    // Add a new document to the shared store
    const fileName = uploadData.get('file').name;
    const fileUrl = '/uploads/' + encodeURIComponent(fileName);
    const newDoc = {
        id: Date.now(),
        subject: uploadData.get('subject'),
        department: uploadData.get('department'),
        code: uploadData.get('code'),
        status: uploadData.get('status'),
        date: new Date().toLocaleDateString(),
        trackingCode: 'TRK-' + Math.random().toString(36).substr(2, 9).toUpperCase(),
        shortDescription: uploadData.get('shortDescription') || '',
        receivedBy: '',
        dateReceived: '',
        timeReceived: '',
        file: {
            name: fileName,
            url: fileUrl
        }
    };
    addDocument(newDoc);
}

function closeUploadModal() {
    showUploadModal.value = false;
}

function getStatusOption(val) {
    const statusOptions = [
        { label: 'Simple', days: 3, color: 'blue', value: 'simple' },
        { label: 'Complex', days: 7, color: 'orange', value: 'complex' },
        { label: 'Highly technical', days: 20, color: 'red', value: 'highly-technical' },
        { label: 'Urgent', days: 1, color: 'purple', value: 'urgent' },
    ];
    return statusOptions.find(opt => opt.value === val);
}

function viewDocument(doc) {
    alert(`Viewing document: ${doc.subject}`);
}
function downloadDocument(doc) {
    alert(`Downloading document: ${doc.subject}`);
}
function editDocument(doc) {
    alert(`Editing document: ${doc.subject}`);
}
async function deleteDocument(doc) {
  const confirmed = await confirmDelete();
  if (confirmed) {
    const index = documents.value.findIndex(d => d.id === doc.id);
    if (index > -1) {
      documents.value.splice(index, 1);
    }
  }
}

function toggleActionMenu(idx) {
    actionMenuOpen.value[idx] = !actionMenuOpen.value[idx];
    nextTick(() => {
        document.addEventListener('click', closeAllMenus);
    });
}
function closeAllMenus(e) {
    actionMenuOpen.value = {};
    document.removeEventListener('click', closeAllMenus);
}

onMounted(fetchDocuments);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <Header />
        <div class="flex flex-1 p-6 ">
            <Sidebar :active-section="activeSection" @section-change="handleSectionChange" />
            <main class="flex-1 ml-6 overflow-auto">
                <div class="bg-white p-6 rounded shadow">
                    <h1 class="text-2xl font-bold mb-6">Outgoing Section</h1>
                    
                    <!-- Search and Filter Section -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <SearchBar v-model="searchQuery" placeholder="Search Documents" />
                            </div>
                            <div class="w-48">
                                <DepartmentFilter v-model="selectedDepartment" placeholder="Filter by Department" />
                            </div>
                            <button
                                @click="handleUpload"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg flex items-center space-x-2 transition-colors duration-200 whitespace-nowrap"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <span>Upload</span>
                            </button>
                            <div class="text-sm text-gray-500">
                                {{ filteredDocuments.length }} of {{ documents.length }} documents
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 mt-4" style="min-width: 1400px;">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border">STATUS</th>
                                <th class="px-4 py-2 border">DATE</th>
                                <th class="px-4 py-2 border">DEPT to</th>
                                <th class="px-4 py-2 border">TRACKING CODE</th>
                                <th class="px-4 py-2 border">CODE</th>
                                <th class="px-4 py-2 border">SUBJECT</th>
                                <th class="px-4 py-2 border">SHORT DESCRIPTION</th>
                                <th class="px-4 py-2 border">RECEIVED BY</th>
                                <th class="px-4 py-2 border">DATE RECEIVED</th>
                                <th class="px-4 py-2 border">TIME RECEIVED</th>
                                <th class="px-4 py-2 border">FILE</th>
                                <th class="px-4 py-2 border">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(doc, idx) in filteredDocuments"
                                :key="idx"
                                :class="doc.dateReceived ? 'bg-red-200 border-l-4 border-red-500' : ''"
                            >
                                <td class="px-4 py-2 border">
                                    <span v-if="doc.dateReceived" class="inline-block px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700">
                                        Received
                                    </span>
                                    <span v-else-if="getStatusOption(doc.status)" :class="[
                                        'inline-block px-2 py-1 rounded text-xs font-semibold',
                                        getStatusOption(doc.status).color === 'blue' ? 'bg-blue-100 text-blue-700' : '',
                                        getStatusOption(doc.status).color === 'orange' ? 'bg-orange-100 text-orange-700' : '',
                                        getStatusOption(doc.status).color === 'red' ? 'bg-red-100 text-red-700' : '',
                                        getStatusOption(doc.status).color === 'purple' ? 'bg-purple-100 text-purple-700' : ''
                                    ]">
                                        {{ getStatusOption(doc.status).label }} - {{ getStatusOption(doc.status).days }} days
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">{{ doc.date || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.deptId || doc.department || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.trackingCode || doc.trackingNo || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.code || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.subject || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.shortDescription || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.receivedBy || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.dateReceived || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.timeReceived || '-' }}</td>
                                <td class="px-4 py-2 border">
                                    <span v-if="doc.file">
                                        {{ doc.file.name || doc.file }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td class="px-4 py-2 border">
                                    <div v-if="doc.dateReceived" class="text-green-700 font-semibold">
                                        Received</div>
                                    <div v-else class="relative">
                                        <button @click.stop="toggleActionMenu(idx)" class="w-10 h-10 flex items-center justify-center rounded hover:bg-gray-200">
                                            <!-- Burger menu icon -->
                                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <rect x="4" y="6" width="16" height="2" rx="1"/>
                                                <rect x="4" y="11" width="16" height="2" rx="1"/>
                                                <rect x="4" y="16" width="16" height="2" rx="1"/>
                                            </svg>
                                        </button>
                                        <div v-if="actionMenuOpen[idx]" class="absolute right-0 bottom-full mb-2 z-10 w-32 bg-white border rounded shadow-lg py-2">
                                            <button @click="editDocument(doc); closeAllMenus()" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Edit</button>
                                            <button @click="deleteDocument(doc); closeAllMenus()" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">Delete</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    
                    <!-- No results message -->
                    <div v-if="searchQuery && filteredDocuments.length === 0" class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2">No documents found matching "{{ searchQuery }}"</p>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Upload Modal -->
        <UploadModal 
            :show="showUploadModal"
            title="Upload Outgoing Document"
            @close="closeUploadModal"
            @upload="handleUploadSubmit"
        />
    </div>
</template>
