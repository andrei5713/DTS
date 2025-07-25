<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: 'Upload Document'
    },
    formData: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['close', 'upload'])

function generateTrackingNumber() {
    const prefix = 'CPMSD';
    const now = new Date();
    const yyyy = now.getFullYear();
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    const dd = String(now.getDate()).padStart(2, '0');
    const rand = Math.floor(1000 + Math.random() * 9000);
    return `${prefix}-${yyyy}-${mm}-${rand}`;
}

const formData = ref({
    trackingCode: '',
    documentType: '',
    subject: '',
    documentDate: '',
    entryDate: '',
    sender: '',
    originatingOffice: '',
    originType: 'internal',
    priority: '',
    remarks: '',
    file: null,
    routing: 'internal',
})

const errors = ref({})
const isUploading = ref(false)

const isDocumentTypeOpen = ref(false)
const isOriginTypeOpen = ref(false)
const isPriorityOpen = ref(false)
const isRoutingOpen = ref(false)

const statusOptions = [
    { label: 'Simple', days: 3, color: 'blue', value: 'simple' },
    { label: 'Complex', days: 7, color: 'orange', value: 'complex' },
    { label: 'Highly technical', days: 20, color: 'red', value: 'highly-technical' },
    { label: 'Urgent', days: 1, color: 'purple', value: 'urgent' },
]

const documentTypes = [
    'Memo',
    'Letter',
    'PR',
    'DV',
]

const priorities = [
    'Normal',
    'Urgent',
    'Confidential',
]

function closeModal() {
    emit('close')
    // Reset only after closing
    formData.value = {
        trackingCode: generateTrackingNumber(),
        documentType: '',
        subject: '',
        documentDate: '',
        entryDate: '',
        sender: '',
        originatingOffice: '',
        originType: 'internal',
        priority: '',
        remarks: '',
        file: null,
        routing: 'internal',
    }
}

function resetForm() {
    formData.value = {
        trackingCode: generateTrackingNumber(),
        documentType: '',
        subject: '',
        documentDate: '',
        entryDate: '',
        sender: '',
        originatingOffice: '',
        originType: 'internal',
        priority: '',
        remarks: '',
        file: null,
        routing: 'internal',
    }
    errors.value = {}
}

function handleFileChange(event) {
    const file = event.target.files[0]
    if (file) {
        if (file.type !== 'application/pdf') {
            errors.value.file = 'Only PDF files are allowed'
            event.target.value = ''
            return
        }
        if (file.size > 10 * 1024 * 1024) {
            errors.value.file = 'File size must be less than 10MB'
            event.target.value = ''
            return
        }
        formData.value.file = file
        errors.value.file = ''
    }
}

function validateForm() {
    errors.value = {}
    if (!formData.value.trackingCode.trim()) {
        errors.value.trackingCode = 'Tracking number is required'
    }
    if (!formData.value.documentType) {
        errors.value.documentType = 'Document type is required'
    }
    if (!formData.value.subject.trim()) {
        errors.value.subject = 'Subject is required'
    }
    if (!formData.value.documentDate) {
        errors.value.documentDate = 'Document date is required'
    }
    if (!formData.value.entryDate) {
        errors.value.entryDate = 'Date of entry is required'
    }
    if (!formData.value.sender.trim()) {
        errors.value.sender = 'Sender is required'
    }
    if (!formData.value.originatingOffice.trim()) {
        errors.value.originatingOffice = 'Originating office is required'
    }
    if (!formData.value.priority) {
        errors.value.priority = 'Priority is required'
    }
    if (!formData.value.file) {
        errors.value.file = 'File is required'
    }
    return Object.keys(errors.value).length === 0
}

async function handleSubmit() {
    if (!validateForm()) {
        return
    }
    isUploading.value = true
    try {
        const uploadData = new FormData()
        Object.entries(formData.value).forEach(([key, value]) => {
            if (key === 'file') {
                uploadData.append('file', value)
            } else {
                uploadData.append(key, value)
            }
        })
        emit('upload', uploadData)
        closeModal()
    } catch (error) {
        console.error('Upload error:', error)
        errors.value.general = 'Upload failed. Please try again.'
    } finally {
        isUploading.value = false
    }
}

watch(() => props.show, (newValue) => {
    if (newValue) {
        // Only reset if not editing
        if (!props.formData) {
            resetForm()
        }
    }
})

watch(() => props.formData, (newVal) => {
  if (newVal) {
    formData.value = { ...newVal };
  } else {
    // Only reset if not editing
    formData.value = {
      trackingCode: generateTrackingNumber(),
      documentType: '',
      subject: '',
      documentDate: '',
      entryDate: '',
      sender: '',
      originatingOffice: '',
      originType: 'internal',
      priority: '',
      remarks: '',
      file: null,
      routing: 'internal',
    };
  }
}, { immediate: true });
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-auto max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b">
          <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Tracking Number -->
              <div>
                <label for="trackingCode" class="block text-sm font-medium text-gray-700 mb-1">Tracking Number *</label>
                <input id="trackingCode" v-model="formData.trackingCode" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" :class="{ 'border-red-500': errors.trackingCode }" />
                <p v-if="errors.trackingCode" class="mt-1 text-sm text-red-600">{{ errors.trackingCode }}</p>
              </div>
              <!-- Document Type -->
              <div class="relative">
                <label for="documentType" class="block text-sm font-medium text-gray-700 mb-1">Document Type *</label>
                <div class="relative">
                  <select id="documentType" v-model="formData.documentType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer pr-12" :class="{ 'border-red-500': errors.documentType }" @focus="isDocumentTypeOpen = true" @blur="isDocumentTypeOpen = false">
                    <option value="">Select Document Type</option>
                    <option v-for="type in documentTypes" :key="type" :value="type">{{ type }}</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                    <svg class="w-5 h-5 text-gray-700 font-bold transition-transform duration-200" :class="{ 'rotate-180': isDocumentTypeOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
                <p v-if="errors.documentType" class="mt-1 text-sm text-red-600">{{ errors.documentType }}</p>
              </div>
              <!-- Subject/Title -->
              <div class="md:col-span-2">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject/Title *</label>
                <input id="subject" v-model="formData.subject" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" :class="{ 'border-red-500': errors.subject }" />
                <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
              </div>
              <!-- Document Date -->
              <div>
                <label for="documentDate" class="block text-sm font-medium text-gray-700 mb-1">Document Date *</label>
                <input id="documentDate" v-model="formData.documentDate" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" :class="{ 'border-red-500': errors.documentDate }" />
                <p v-if="errors.documentDate" class="mt-1 text-sm text-red-600">{{ errors.documentDate }}</p>
              </div>
              <!-- Date of Entry -->
              <div>
                <label for="entryDate" class="block text-sm font-medium text-gray-700 mb-1">Date of Entry *</label>
                <input id="entryDate" v-model="formData.entryDate" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" :class="{ 'border-red-500': errors.entryDate }" />
                <p v-if="errors.entryDate" class="mt-1 text-sm text-red-600">{{ errors.entryDate }}</p>
              </div>
              <!-- Sender -->
              <div>
                <label for="sender" class="block text-sm font-medium text-gray-700 mb-1">Sender *</label>
                <input id="sender" v-model="formData.sender" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" :class="{ 'border-red-500': errors.sender }" />
                <p v-if="errors.sender" class="mt-1 text-sm text-red-600">{{ errors.sender }}</p>
              </div>
              <!-- Originating Office -->
              <div>
                <label for="originatingOffice" class="block text-sm font-medium text-gray-700 mb-1">Originating Office *</label>
                <input id="originatingOffice" v-model="formData.originatingOffice" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" :class="{ 'border-red-500': errors.originatingOffice }" />
                <p v-if="errors.originatingOffice" class="mt-1 text-sm text-red-600">{{ errors.originatingOffice }}</p>
              </div>
              <!-- Origin Type -->
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Origin Type *</label>
                <div class="relative">
                  <select v-model="formData.originType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer pr-12" @focus="isOriginTypeOpen = true" @blur="isOriginTypeOpen = false">
                    <option value="internal">Internal</option>
                    <option value="external">External</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                    <svg class="w-5 h-5 text-gray-700 font-bold transition-transform duration-200" :class="{ 'rotate-180': isOriginTypeOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <!-- Priority Level -->
              <div class="relative">
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority Level *</label>
                <div class="relative">
                  <select id="priority" v-model="formData.priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer pr-12" :class="{ 'border-red-500': errors.priority }" @focus="isPriorityOpen = true" @blur="isPriorityOpen = false">
                    <option value="">Select Priority</option>
                    <option v-for="level in priorities" :key="level" :value="level">{{ level }}</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                    <svg class="w-5 h-5 text-gray-700 font-bold transition-transform duration-200" :class="{ 'rotate-180': isPriorityOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
                <p v-if="errors.priority" class="mt-1 text-sm text-red-600">{{ errors.priority }}</p>
              </div>
              <!-- Routing -->
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Routing *</label>
                <div class="relative">
                  <select v-model="formData.routing" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer pr-12" @focus="isRoutingOpen = true" @blur="isRoutingOpen = false">
                    <option value="internal">Internal</option>
                    <option value="external">External</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                    <svg class="w-5 h-5 text-gray-700 font-bold transition-transform duration-200" :class="{ 'rotate-180': isRoutingOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <!-- Remarks/Description -->
              <div class="md:col-span-2">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks/Description</label>
                <textarea id="remarks" v-model="formData.remarks" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="2"></textarea>
              </div>
              <!-- File Upload -->
              <div class="md:col-span-2">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File (PDF only) *</label>
                <input id="file" type="file" accept=".pdf" @change="handleFileChange" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" :class="{ 'border-red-500': errors.file }" />
                <p v-if="errors.file" class="mt-1 text-sm text-red-600">{{ errors.file }}</p>
                <p v-if="formData.file" class="mt-1 text-sm text-green-600">Selected: {{ formData.file.name }}</p>
              </div>
            </div>
            <!-- General Error -->
            <div v-if="errors.general" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
              <p class="text-sm text-red-600">{{ errors.general }}</p>
            </div>
            <!-- Actions -->
            <div class="flex justify-end space-x-3 mt-6">
              <button type="button" @click="closeModal" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-400 border-2 border-black-200 rounded-md transition-colors" :disabled="isUploading">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2" :disabled="isUploading">
                <svg v-if="isUploading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isUploading ? 'Uploading...' : 'Upload' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
select.appearance-none {
    background-image: none;
}
.rotate-180 {
  transform: rotate(180deg);
}
</style> 