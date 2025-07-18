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
    }
})

const emit = defineEmits(['close', 'upload'])

const formData = ref({
    subject: '',
    department: '',
    code: '',
    status: '',
    shortDescription: '',
    trackingCode: '',
    file: null
})

const errors = ref({})
const isUploading = ref(false)

// Add refs to track open state for Department and Status selects
const isDepartmentOpen = ref(false)
const isStatusOpen = ref(false)

const statusOptions = [
    { label: 'Simple', days: 3, color: 'blue', value: 'simple' },
    { label: 'Complex', days: 7, color: 'orange', value: 'complex' },
    { label: 'Highly technical', days: 20, color: 'red', value: 'highly-technical' },
    { label: 'Urgent', days: 1, color: 'purple', value: 'urgent' },
]

const departments = [
    'AO',
    'ODA',
    'OAAFA',
    'OAAO',
    'AGSD',
    'CPMSD',
    'FD',
    'IAD',
    'OCD',
]

function closeModal() {
    emit('close')
    resetForm()
}

function resetForm() {
    formData.value = {
        subject: '',
        department: '',
        code: '',
        status: '',
        shortDescription: '',
        trackingCode: '',
        file: null
    }
    errors.value = {}
}

function handleFileChange(event) {
    const file = event.target.files[0]
    if (file) {
        // Check if file is PDF
        if (file.type !== 'application/pdf') {
            errors.value.file = 'Only PDF files are allowed'
            event.target.value = ''
            return
        }
        
        // Check file size (max 10MB)
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
    
    if (!formData.value.subject.trim()) {
        errors.value.subject = 'Subject is required'
    }
    
    if (!formData.value.department) {
        errors.value.department = 'Department is required'
    }
    
    if (!formData.value.code.trim()) {
        errors.value.code = 'Code is required'
    }
    
    if (!formData.value.status) {
        errors.value.status = 'Status is required'
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
        // Create FormData for file upload
        const uploadData = new FormData()
        uploadData.append('subject', formData.value.subject)
        uploadData.append('department', formData.value.department)
        uploadData.append('code', formData.value.code)
        uploadData.append('status', formData.value.status)
        uploadData.append('shortDescription', formData.value.shortDescription)
        uploadData.append('trackingCode', formData.value.trackingCode)
        uploadData.append('file', formData.value.file)
        
        // Emit the upload event with the form data
        emit('upload', uploadData)
        
        // Close modal and reset form
        closeModal()
    } catch (error) {
        console.error('Upload error:', error)
        errors.value.general = 'Upload failed. Please try again.'
    } finally {
        isUploading.value = false
    }
}

// Watch for show prop changes to reset form when modal opens
watch(() => props.show, (newValue) => {
    if (newValue) {
        resetForm()
    }
})
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                    <button
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="p-6">
                    <form @submit.prevent="handleSubmit">
                        <!-- Subject -->
                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Subject *
                            </label>
                            <input
                                id="subject"
                                v-model="formData.subject"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': errors.subject }"
                            />
                            <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
                        </div>
                        
                        <!-- Department -->
                        <div class="mb-4 relative">
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                Department *
                            </label>
                            <div class="relative">
                                <select
                                    id="department"
                                    v-model="formData.department"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer pr-10"
                                    :class="{ 'border-red-500': errors.department }"
                                    @focus="isDepartmentOpen = true"
                                    @blur="isDepartmentOpen = false"
                                >
                                    <option value="">Select Department</option>
                                    <option v-for="dept in departments" :key="dept" :value="dept">
                                        {{ dept }}
                                    </option>
                                </select>
                                <!-- Custom Chevron -->
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg :class="[ 'w-5 h-5 transition-transform duration-200', isDepartmentOpen ? 'rotate-180' : 'rotate-0' ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                            <p v-if="errors.department" class="mt-1 text-sm text-red-600">{{ errors.department }}</p>
                        </div>
                        
                        <!-- Code -->
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                Code *
                            </label>
                            <input
                                id="code"
                                v-model="formData.code"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': errors.code }"
                            />
                            <p v-if="errors.code" class="mt-1 text-sm text-red-600">{{ errors.code }}</p>
                        </div>
                        
                        <!-- Status -->
                        <div class="mb-4 relative">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status *
                            </label>
                            <div class="relative">
                                <select
                                    id="status"
                                    v-model="formData.status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer pr-10"
                                    :class="{ 'border-red-500': errors.status }"
                                    @focus="isStatusOpen = true"
                                    @blur="isStatusOpen = false"
                                >
                                    <option value="">Select Status</option>
                                    <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                        {{ status.label }} - {{ status.days }} days
                                    </option>
                                </select>
                                <!-- Custom Chevron -->
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg :class="[ 'w-5 h-5 transition-transform duration-200', isStatusOpen ? 'rotate-180' : 'rotate-0' ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                            <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
                        </div>
                        
                        <!-- Short Description -->
                        <div class="mb-4">
                            <label for="shortDescription" class="block text-sm font-medium text-gray-700 mb-2">
                                Short Description *
                            </label>
                            <input
                                id="shortDescription"
                                v-model="formData.shortDescription"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': errors.shortDescription }"
                            />
                            <p v-if="errors.shortDescription" class="mt-1 text-sm text-red-600">{{ errors.shortDescription }}</p>
                        </div>
                        
                        
                        
                        <!-- Tracking Code (Read-only) -->
                        <div class="mb-4">
                            <label for="trackingCode" class="block text-sm font-medium text-gray-700 mb-2">
                                Tracking Code *
                            </label>
                            <input
                                id="trackingCode"
                                v-model="formData.trackingCode"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p v-if="errors.trackingCode" class="mt-1 text-sm text-red-600">{{ errors.trackingCode }}</p>
                        </div>
                        
                        <!-- File Upload -->
                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                File (PDF only) *
                            </label>
                            <input
                                id="file"
                                type="file"
                                accept=".pdf"
                                @change="handleFileChange"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                :class="{ 'border-red-500': errors.file }"
                            />
                            <p v-if="errors.file" class="mt-1 text-sm text-red-600">{{ errors.file }}</p>
                            <p v-if="formData.file" class="mt-1 text-sm text-green-600">
                                Selected: {{ formData.file.name }}
                            </p>
                        </div>
                        
                        <!-- General Error -->
                        <div v-if="errors.general" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-600">{{ errors.general }}</p>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-400 border-2 border-black-200 rounded-md transition-colors"
                                :disabled="isUploading"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                                :disabled="isUploading"
                            >
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
/* Hide default select arrow for custom chevron */
select.appearance-none {
    background-image: none;
}
</style> 