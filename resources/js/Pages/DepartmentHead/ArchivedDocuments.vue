<template>
  <div data-archived-documents>
    <h2 class="text-2xl font-bold mb-2">Archived Documents</h2>
    <div class="flex items-center justify-between mb-4">
      <SearchBar v-model="searchQuery" placeholder="Search archived documents..." />
      <div class="flex items-center space-x-1">
        <button 
          @click="viewMode = 'table'"
          :class="[
            'p-2 rounded-md transition-colors',
            viewMode === 'table' 
              ? 'bg-blue-500 text-white' 
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
          title="Table View"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a1 1 0 011-1h16a1 1 0 011 1v16a1 1 0 01-1 1H4a1 1 0 01-1-1z"></path>
          </svg>
        </button>
        <button 
          @click="viewMode = 'cards'"
          :class="[
            'p-2 rounded-md transition-colors',
            viewMode === 'cards' 
              ? 'bg-blue-500 text-white' 
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
          title="Card View"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Archived Documents Content -->
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Archived Documents</h3>
        <p class="text-sm text-gray-600 mt-1">Documents that have been archived</p>
      </div>
      
      <!-- Table View -->
      <div v-if="viewMode === 'table'">
        <Table :columns="archivedColumns" :rows="filteredArchivedDocuments">
          <template #duration="{ row }">
            <div 
              v-if="row.status === 'received' && getReceivedDate(row)"
              @click="openTimerModal(row)"
              class="cursor-pointer"
            >
              <div 
                class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md transition-transform hover:scale-105"
                :class="calculateTimerStatus(row).color"
                :title="calculateTimerStatus(row).isOverdue ? 'Overdue' : `${calculateTimerStatus(row).daysRemaining} days remaining`"
              >
                {{ calculateTimerStatus(row).isOverdue ? '0' : calculateTimerStatus(row).daysRemaining }}
              </div>
            </div>
            <div v-else class="w-12 h-12 rounded-lg flex items-center justify-center bg-gray-200 text-gray-500 text-sm">
              -
            </div>
          </template>
          <template #priority="{ row }">
            <span class="inline-flex items-center gap-2 text-sm font-medium text-gray-900">
              <span class="w-2.5 h-2.5 rounded-full" :class="getPriorityCircleColor(row.priority)"></span>
              {{ row.priority || '-' }}
            </span>
          </template>
          <template #status="{ row }">
            <span class="text-sm font-semibold capitalize" :class="getStatusTextColor(row.status)">
              {{ row.status || '-' }}
            </span>
          </template>
          <template #ACTIONS="{ row }">
            <div class="flex gap-2">
              <button 
                @click="viewDocument(row)"
                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
                View
              </button>
              <button 
                @click="downloadDocument(row)"
                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                Download
              </button>
              <button 
                @click="unarchiveDocument(row.id)"
                class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-full shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-green-300"
                title="Unarchive Document"
              >
                <ArchiveRestore class="w-4 h-4" />
              </button>
            </div>
          </template>
        </Table>
      </div>
      
      <!-- Card View -->
      <div v-else-if="viewMode === 'cards'" class="p-6">
        <div v-if="filteredArchivedDocuments.length === 0" class="text-center py-12">
          <div class="text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No archived documents</h3>
          <p class="text-sm text-gray-500">Documents will appear here once they have been archived.</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="document in filteredArchivedDocuments" 
            :key="document.id"
            class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-3">
              <div class="flex-1">
                <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ document.subject }}</h4>
                <p class="text-xs text-gray-600">{{ document.tracking_code }}</p>
              </div>
              <span class="inline-flex items-center gap-2 text-xs" :class="getPriorityTextColor(document.priority)">
                <span class="w-2.5 h-2.5 rounded-full" :class="getPriorityCircleColor(document.priority)"></span>
                {{ document.priority }}
              </span>
            </div>
            
            <div class="space-y-2 mb-4">
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">Type:</span>
                <span class="text-gray-700">{{ document.document_type }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">Uploaded By:</span>
                <span class="text-gray-700">{{ document.upload_by }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">Sent To:</span>
                <span class="text-gray-700">{{ document.upload_to }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">Office:</span>
                <span class="text-gray-700">{{ document.originating_office }}</span>
              </div>
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">Status:</span>
                <span class="text-gray-700 capitalize">{{ document.status }}</span>
              </div>
            </div>
            
            <div class="flex gap-2">
              <button 
                @click="viewDocument(document)"
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-md text-xs font-medium transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
                View
              </button>
              <button 
                @click="downloadDocument(document)"
                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-md text-xs font-medium transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                Download
              </button>
              <button 
                @click="unarchiveDocument(document.id)"
                class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-green-300"
                title="Unarchive Document"
              >
                <ArchiveRestore class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="viewMode === 'table' && filteredArchivedDocuments.length === 0" class="px-6 py-12 text-center">
        <div class="text-gray-400 mb-4">
          <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No archived documents</h3>
        <p class="text-sm text-gray-500">Documents will appear here once they have been archived.</p>
      </div>
    </div>

    <!-- PDF Preview Modal -->
    <EnhancedPdfViewer 
      v-if="showPdfModal" 
      :document="pdfDocument" 
      :pdf-url="pdfUrl" 
      @close="closePdfModal" 
    />
    
    <!-- Timer Modal -->
    <div v-if="showTimerModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeTimerModal"></div>
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Document Timer</h3>
            <p class="text-sm text-gray-600 mt-1">{{ timerDocument?.subject }}</p>
          </div>
          <div class="p-6">
            <div v-if="timerDocument" class="space-y-4">
              <div v-if="timerDocument.status === 'received' && getReceivedDate(timerDocument)" class="space-y-3">
                <!-- Live Countdown Timer -->
                <div :class="calculateTimerStatus(timerDocument).isOverdue ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-blue-500 to-blue-600'" class="rounded-lg p-6 text-white">
                  <h4 class="text-sm font-medium text-white/90 mb-3 text-center">{{ calculateTimerStatus(timerDocument).isOverdue ? 'Time Expired' : 'Time Remaining' }}</h4>
                  <div class="flex items-center justify-center gap-4">
                    <div class="text-center">
                      <div class="text-4xl font-bold">{{ String(timerCountdown.days).padStart(2, '0') }}</div>
                      <div class="text-xs font-medium text-white/80 mt-1">Days</div>
                    </div>
                    <div class="text-3xl font-bold">:</div>
                    <div class="text-center">
                      <div class="text-4xl font-bold">{{ String(timerCountdown.hours).padStart(2, '0') }}</div>
                      <div class="text-xs font-medium text-white/80 mt-1">Hours</div>
                    </div>
                    <div class="text-3xl font-bold">:</div>
                    <div class="text-center">
                      <div class="text-4xl font-bold">{{ String(timerCountdown.minutes).padStart(2, '0') }}</div>
                      <div class="text-xs font-medium text-white/80 mt-1">Minutes</div>
                    </div>
                    <div class="text-3xl font-bold">:</div>
                    <div class="text-center">
                      <div class="text-4xl font-bold">{{ String(timerCountdown.seconds).padStart(2, '0') }}</div>
                      <div class="text-xs font-medium text-white/80 mt-1">Seconds</div>
                    </div>
                  </div>
                </div>
                
                <div class="flex items-center justify-between p-4 rounded-lg" :class="calculateTimerStatus(timerDocument).isOverdue ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-blue-50 border-2 border-blue-300'">
                  <div class="flex-1 flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-700 mb-1">Priority Level</h4>
                      <p class="text-base font-semibold text-gray-900">{{ timerDocument.priority }}</p>
                    </div>
                    <div class="text-right">
                      <h4 class="text-sm font-medium text-gray-700 mb-1">Processing Time</h4>
                      <p class="text-base font-semibold text-gray-900">{{ getPriorityDays(timerDocument.priority) }} days</p>
                    </div>
                  </div>
                  <div 
                    class="w-16 h-16 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-md ml-4 flex-shrink-0"
                    :class="calculateTimerStatus(timerDocument).color"
                  >
                    {{ calculateTimerStatus(timerDocument).isOverdue ? '0' : calculateTimerStatus(timerDocument).daysRemaining }}
                  </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 mt-4">
                  <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                      <h4 class="text-sm font-medium text-gray-700 mb-2">Received By</h4>
                      <p class="text-sm text-gray-900 font-semibold">{{ timerDocument.current_recipient?.name || timerDocument.upload_to_user?.name || timerDocument.upload_to || 'N/A' }}</p>
                      <p class="text-xs text-gray-600 mt-1">
                        {{ getReceivedDate(timerDocument).toLocaleString() }}
                      </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                      <p class="text-xs text-gray-600 mb-2">Handling Duration</p>
                      <div class="flex items-center gap-1.5">
                        <div class="flex items-center gap-0.5">
                          <span class="text-sm font-bold text-gray-900">{{ handlingDuration.days }}</span>
                          <span class="text-xs text-gray-600">d</span>
                        </div>
                        <span class="text-gray-400">:</span>
                        <div class="flex items-center gap-0.5">
                          <span class="text-sm font-bold text-gray-900">{{ String(handlingDuration.hours).padStart(2, '0') }}</span>
                          <span class="text-xs text-gray-600">h</span>
                        </div>
                        <span class="text-gray-400">:</span>
                        <div class="flex items-center gap-0.5">
                          <span class="text-sm font-bold text-gray-900">{{ String(handlingDuration.minutes).padStart(2, '0') }}</span>
                          <span class="text-xs text-gray-600">m</span>
                        </div>
                        <span class="text-gray-400">:</span>
                        <div class="flex items-center gap-0.5">
                          <span class="text-sm font-bold text-gray-900">{{ String(handlingDuration.seconds).padStart(2, '0') }}</span>
                          <span class="text-xs text-gray-600">s</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-8 text-gray-500">
                <p>Timer starts when document is accepted/received</p>
              </div>
            </div>
          </div>
          <div class="px-6 py-4 border-t flex justify-end">
            <button 
              @click="closeTimerModal" 
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { ArchiveRestore } from 'lucide-vue-next';
import Table from '@/Components/Table.vue';
import SearchBar from '@/Components/SearchBar.vue';
import EnhancedPdfViewer from '@/Components/EnhancedPdfViewer.vue';

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

// Get CSRF token with fallback
function getCSRFToken() {
  if (page.props.csrf_token) {
    return page.props.csrf_token;
  }
  
  const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (metaToken) {
    return metaToken;
  }
  
  console.warn('No CSRF token found');
  return '';
}

const documents = ref([]);
const searchQuery = ref('');
const viewMode = ref('table');
const showPdfModal = ref(false);
const pdfDocument = ref(null);
const pdfUrl = ref('');

// Timer modal state
const showTimerModal = ref(false);
const timerDocument = ref(null);
const timerCountdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
const handlingDuration = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
let timerInterval = null;

const archivedColumns = [
  { label: 'DURATION', key: 'duration' },
  { label: 'TRACKING CODE', key: 'tracking_code' },
  { label: 'DOCUMENT TYPE', key: 'document_type' },
  { label: 'SUBJECT', key: 'subject' },
  { label: 'UPLOADED BY', key: 'upload_by' },
  { label: 'SENT TO', key: 'upload_to' },
  { label: 'ORIGINATING OFFICE', key: 'originating_office' },
  { label: 'PRIORITY', key: 'priority' },
  { label: 'STATUS', key: 'status' },
  { label: 'ACTIONS', key: 'ACTIONS' },
];

// Transform documents to include unit field for display
const transformedDocuments = computed(() => {
  return documents.value.map(doc => ({
    ...doc,
    unit: doc.upload_to_user?.unit?.full_name || 'N/A'
  }));
});

// Filter archived documents with search functionality
const filteredArchivedDocuments = computed(() => {
  let docs = transformedDocuments.value.filter(doc => doc.status === 'archived');
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    docs = docs.filter(doc =>
      Object.values(doc).some(val =>
        String(val).toLowerCase().includes(q)
      )
    );
  }
  return docs;
});

async function fetchDocuments() {
  try {
    const response = await fetch('/documents/archived', {
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    });
    
    if (!response.ok) {
      console.error('Failed to fetch archived documents:', response.status, response.statusText);
      return;
    }
    
    const data = await response.json();
    documents.value = data.documents || [];
  } catch (error) {
    console.error('Error fetching archived documents:', error);
  }
}

function viewDocument(document) {
  pdfDocument.value = document;
  if (document.file_path) {
    pdfUrl.value = `/storage/${document.file_path}`;
  } else {
    pdfUrl.value = "";
  }
  showPdfModal.value = true;
}

function downloadDocument(document) {
  if (document.file_path) {
    const link = document.createElement('a');
    link.href = `/storage/${document.file_path}`;
    link.download = document.file_name || 'document.pdf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
}

async function unarchiveDocument(documentId) {
  if (!confirm('Are you sure you want to unarchive this document? This will move it back to the incoming section.')) {
    return;
  }
  
  try {
    const csrfToken = getCSRFToken();
    console.log('CSRF Token for unarchive:', csrfToken);
    
    if (!csrfToken) {
      console.error('CSRF token not found');
      return;
    }
    
    const response = await fetch(`/documents/${documentId}/unarchive`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        unarchive_notes: 'Document unarchived by user action'
      })
    });
    
    if (response.ok) {
      const data = await response.json();
      console.log('Document unarchived successfully:', data.message);
      await fetchDocuments(); // Refresh the documents list
      
      // Trigger a custom event to refresh incoming documents
      window.dispatchEvent(new CustomEvent('refreshIncomingDocuments'));
    } else {
      const errorData = await response.json().catch(() => ({}));
      console.error('Error unarchiving document:', errorData.message);
    }
  } catch (error) {
    console.error('Error unarchiving document:', error);
  }
}

// ARTA Color Palette for Priorities - Circle Indicator
// Simple (3 days) → Blue
// Complex (7 days) → Red
// Highly Technical (20 days) → Yellow
function getPriorityCircleColor(priority) {
  if (!priority) return 'bg-gray-400'
  
  const priorityLower = priority.toLowerCase()
  
  if (priorityLower.includes('simple') || priorityLower.includes('3 days')) {
    return 'bg-blue-500'
  } else if (priorityLower.includes('complex') || priorityLower.includes('7 days')) {
    return 'bg-red-500'
  } else if (priorityLower.includes('highly technical') || priorityLower.includes('20 days')) {
    return 'bg-yellow-500'
  }
  
  return 'bg-gray-400'
}

function getPriorityTextColor(priority) {
  if (!priority) return 'text-gray-600'
  
  const priorityLower = priority.toLowerCase()
  
  if (priorityLower.includes('simple') || priorityLower.includes('3 days')) {
    return 'text-blue-600 font-semibold'
  } else if (priorityLower.includes('complex') || priorityLower.includes('7 days')) {
    return 'text-red-600 font-semibold'
  } else if (priorityLower.includes('highly technical') || priorityLower.includes('20 days')) {
    return 'text-yellow-600 font-semibold'
  }
  
  return 'text-gray-600'
}

function getStatusTextColor(status) {
  if (!status) return 'text-gray-600'
  
  const statusLower = status.toLowerCase()
  
  if (statusLower === 'pending') {
    return 'text-yellow-600'
  } else if (statusLower === 'received') {
    return 'text-green-600'
  } else if (statusLower === 'rejected') {
    return 'text-red-600'
  } else if (statusLower === 'forwarded') {
    return 'text-blue-600'
  } else if (statusLower === 'complied') {
    return 'text-purple-600'
  } else if (statusLower === 'archived') {
    return 'text-gray-600'
  }
  
  return 'text-gray-600'
}

// Timer functions
function getPriorityDays(priority) {
  if (!priority) return 0
  
  const priorityLower = priority.toLowerCase()
  
  if (priorityLower.includes('simple') || priorityLower.includes('3 days')) {
    return 3
  } else if (priorityLower.includes('complex') || priorityLower.includes('7 days')) {
    return 7
  } else if (priorityLower.includes('highly technical') || priorityLower.includes('20 days')) {
    return 20
  }
  
  return 0
}

function getReceivedDate(document) {
  // Only use accepted_by_do_at - this is the actual timestamp when document was accepted/received
  // Don't use updated_at as it may change for other reasons
  if (document.accepted_by_do_at) {
    // Parse the date string - handle both ISO format and other formats
    const date = new Date(document.accepted_by_do_at)
    // Check if date is valid
    if (isNaN(date.getTime())) {
      console.error('Invalid accepted_by_do_at date:', document.accepted_by_do_at)
      return null
    }
    return date
  }
  // If no accepted_by_do_at, document hasn't been accepted yet - return null
  return null
}

function calculateTimerStatus(document) {
  if (!document || document.status !== 'received') {
    return { color: 'bg-gray-300', daysRemaining: 0, daysElapsed: 0, isOverdue: false, receivedDate: null, priorityDays: 0 }
  }
  
  const priorityDays = getPriorityDays(document.priority)
  const receivedDate = getReceivedDate(document)
  
  if (!receivedDate || priorityDays === 0) {
    return { color: 'bg-gray-300', daysRemaining: 0, daysElapsed: 0, isOverdue: false, receivedDate: null, priorityDays: 0 }
  }
  
  const now = new Date()
  // Reset time to start of day for accurate day calculation
  const receivedStartOfDay = new Date(receivedDate)
  receivedStartOfDay.setHours(0, 0, 0, 0)
  const nowStartOfDay = new Date(now)
  nowStartOfDay.setHours(0, 0, 0, 0)
  
  const diffTime = nowStartOfDay - receivedStartOfDay
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))
  const daysRemaining = priorityDays - diffDays
  const isOverdue = diffDays >= priorityDays
  
  return {
    color: isOverdue ? 'bg-yellow-500' : 'bg-blue-500',
    daysRemaining: isOverdue ? 0 : daysRemaining,
    daysElapsed: diffDays,
    isOverdue: isOverdue,
    receivedDate: receivedDate,
    priorityDays: priorityDays
  }
}

function openTimerModal(document) {
  timerDocument.value = document
  showTimerModal.value = true
  startTimerCountdown(document)
}

function startTimerCountdown(document) {
  // Clear any existing interval
  if (timerInterval) {
    clearInterval(timerInterval)
    timerInterval = null
  }
  
  // Only start timer if document is received
  if (!document || document.status !== 'received') {
    timerCountdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
    return
  }
  
  const priorityDays = getPriorityDays(document.priority)
  const receivedDate = getReceivedDate(document)
  
  if (!receivedDate || priorityDays === 0) {
    timerCountdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
    return
  }
  
  // Calculate deadline based on priority days from the exact received timestamp
  // Add the full priority days to the exact received time
  // For example: if received at 5:47 AM on Jan 1 with 3-day priority, deadline is 5:47 AM on Jan 4
  const deadline = new Date(receivedDate)
  deadline.setDate(deadline.getDate() + priorityDays)
  
  // Update countdown immediately
  updateCountdown(deadline)
  updateHandlingDuration()
  
  // Update countdown every second
  timerInterval = setInterval(() => {
    updateCountdown(deadline)
  }, 1000)
}

function updateCountdown(deadline) {
  const now = new Date()
  let diff = deadline - now
  
  if (diff <= 0) {
    // Timer has expired - show 00:00:00:00
    timerCountdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
    // Don't clear the interval so it keeps showing 00:00:00:00
  } else {
    // Calculate days, hours, minutes, seconds from the difference
    const totalSeconds = Math.floor(diff / 1000)
    const days = Math.floor(totalSeconds / (24 * 60 * 60))
    const hours = Math.floor((totalSeconds % (24 * 60 * 60)) / (60 * 60))
    const minutes = Math.floor((totalSeconds % (60 * 60)) / 60)
    const seconds = totalSeconds % 60
    
    timerCountdown.value = { days, hours, minutes, seconds }
  }
  
  // Update handling duration (how long user has been handling the document)
  updateHandlingDuration()
}

function updateHandlingDuration() {
  if (!timerDocument.value) return
  
  const receivedDate = getReceivedDate(timerDocument.value)
  if (!receivedDate) {
    handlingDuration.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
    return
  }
  
  const now = new Date()
  const diff = now - receivedDate
  
  if (diff <= 0) {
    handlingDuration.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
    return
  }
  
  // Calculate how long the document has been handled
  const totalSeconds = Math.floor(diff / 1000)
  const days = Math.floor(totalSeconds / (24 * 60 * 60))
  const hours = Math.floor((totalSeconds % (24 * 60 * 60)) / (60 * 60))
  const minutes = Math.floor((totalSeconds % (60 * 60)) / 60)
  const seconds = totalSeconds % 60
  
  handlingDuration.value = { days, hours, minutes, seconds }
}

function closeTimerModal() {
  showTimerModal.value = false
  if (timerInterval) {
    clearInterval(timerInterval)
    timerInterval = null
  }
  timerCountdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
  handlingDuration.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
}

function closePdfModal() {
  showPdfModal.value = false;
  pdfDocument.value = null;
  pdfUrl.value = "";
}

onMounted(() => {
  fetchDocuments();
  
  // Listen for custom refresh event
  window.addEventListener('refreshArchivedDocuments', fetchDocuments);
  
  // Watch for visibility changes to refresh data
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        fetchDocuments();
      }
    });
  });
  
  // Observe the component root
  const componentRoot = document.querySelector('[data-archived-documents]');
  if (componentRoot) {
    observer.observe(componentRoot);
  }
});

// Clean up event listener
onUnmounted(() => {
  window.removeEventListener('refreshArchivedDocuments', fetchDocuments);
  if (timerInterval) {
    clearInterval(timerInterval);
    timerInterval = null;
  }
});

// Watch for modal close to clean up timer
watch(showTimerModal, (newValue) => {
  if (!newValue && timerInterval) {
    clearInterval(timerInterval);
    timerInterval = null;
    timerCountdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
    handlingDuration.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
  }
});
</script>
