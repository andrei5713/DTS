<script setup>
import { ref, onMounted, computed } from 'vue'
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
import SearchBar from '@/Components/SearchBar.vue';
import DepartmentFilter from '@/Components/DepartmentFilter.vue';
import { useDocuments } from '@/composables/useDocuments.js';

const activeSection = ref('incoming');
const searchQuery = ref('');
const selectedDepartment = ref('');
const { documents, receiveDocument } = useDocuments();

// Computed property to filter documents based on search query and department
const filteredDocuments = computed(() => {
    let filtered = documents.value;
    
    // Filter by department if selected
    if (selectedDepartment.value) {
        filtered = filtered.filter(doc => 
            doc.deptFrom === selectedDepartment.value || 
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
            doc.deptFrom?.toLowerCase().includes(query) ||
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
        const res = await fetch('/api/documents');
        documents.value = await res.json();
    } catch (e) {
        console.error('Failed to fetch documents', e);
    }
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

function handleReceive(doc) {
    receiveDocument(doc.id);
}

function viewDocument(doc) {
    console.log('Viewing document:', doc);
    // Implement document viewing logic
    alert(`Viewing document: ${doc.subject}`);
}

function downloadDocument(doc) {
    console.log('Downloading document:', doc);
    // Implement document download logic
    alert(`Downloading document: ${doc.subject}`);
}

function editDocument(doc) {
    console.log('Editing document:', doc);
    // Implement document editing logic
    alert(`Editing document: ${doc.subject}`);
}

function deleteDocument(doc) {
    if (confirm(`Are you sure you want to delete "${doc.subject}"?`)) {
        console.log('Deleting document:', doc);
        // Implement document deletion logic
        const index = documents.value.findIndex(d => d.id === doc.id);
        if (index > -1) {
            documents.value.splice(index, 1);
        }
    }
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
                    <h1 class="text-2xl font-bold mb-4">Incoming Section</h1>
                    
                    <!-- Search and Filter Section -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <SearchBar v-model="searchQuery" placeholder="Search Documents" />
                            </div>
                            <div class="w-48">
                                <DepartmentFilter v-model="selectedDepartment" placeholder="Filter by Department" />
                            </div>
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
                                <th class="px-4 py-2 border">DEPT from</th>
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
                                <td class="px-4 py-2 border">{{ doc.deptFrom || doc.department || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.trackingCode || doc.trackingNo || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.code || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.subject || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.shortDescription || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.receivedBy || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.dateReceived || '-' }}</td>
                                <td class="px-4 py-2 border">{{ doc.timeReceived || '-' }}</td>
                                <td class="px-4 py-2 border">
                                    <span v-if="doc.file">
                                        <a
                                            :href="doc.file.url || '#'"
                                            :download="doc.file.name || 'document.pdf'"
                                            class="text-blue-600 hover:underline"
                                            v-if="doc.file.url"
                                        >
                                            {{ doc.file.name || doc.file }}
                                        </a>
                                        <span v-else>{{ doc.file.name || doc.file }}</span>
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td class="px-4 py-2 border">
                                    <button v-if="!doc.dateReceived" @click="handleReceive(doc)" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                                        Receive
                                    </button>
                                    <span v-else class="text-green-700 font-semibold">Received</span>
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
    </div>
</template>