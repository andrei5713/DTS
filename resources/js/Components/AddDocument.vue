<script setup>
import { ref, reactive, watch } from 'vue'
import { ArrowLeft } from 'lucide-vue-next'

const props = defineProps({
  editMode: Boolean,
  doc: Object
})

const emit = defineEmits(['close', 'add', 'save'])

const fileInput = ref(null)

const form = reactive({
  date: '',
  docType: 'Incoming',
  subject: '',
  fromDept: '',
  fromDiv: '',
  memoCode: '',
  toDept: '',
  toDiv: '',
  tracking: '',
  shortDesc: '',
  receivedBy: '',
  receivedDate: '',
  file: null
})

const errors = reactive({})
const departments = ['HR', 'Finance', 'Admin', 'Legal', 'Procurement']
const divisions = ['Division A', 'Division B', 'Division C']

watch(() => props.doc, (newDoc) => {
  if (props.editMode && newDoc) {
    Object.keys(form).forEach(key => {
      form[key] = newDoc[key] ?? ''
    })
    form.file = newDoc.file ? { name: newDoc.file } : null
  }
}, { immediate: true })

watch(() => props.editMode, (isEdit) => {
  if (!isEdit) resetForm()
})

function resetForm() {
  Object.keys(form).forEach(key => {
    form[key] = key === 'docType' ? 'Incoming' : ''
  })
  form.file = null
  if (fileInput.value) fileInput.value.value = ''
}

function onFileChange(event) {
  const file = event.target.files[0]
  if (file && file.type === 'application/pdf') {
    form.file = file
    errors.file = ''
  } else {
    form.file = null
    if (fileInput.value) fileInput.value.value = ''
    errors.file = 'Only PDF files are allowed.'
  }
}

function submitDoc() {
  Object.keys(form).forEach(key => (errors[key] = ''))

  const requiredFields = [
    'date', 'docType', 'subject', 'fromDept', 'fromDiv', 'toDept', 'toDiv',
    'shortDesc', 'receivedBy', 'receivedDate'
  ]

  let isValid = true
  for (const field of requiredFields) {
    if (!form[field]) {
      errors[field] = 'This field is required.'
      isValid = false
    }
  }
  if (!form.file) {
    errors.file = 'Please attach a PDF file.'
    isValid = false
  }

  if (!isValid) return

  const docPayload = {
  status: props.editMode ? 'Updated' : 'New',
  date: form.date,
  toDept: form.toDept,
  code: form.memoCode || form.tracking || '—',
  subject: form.subject,
  shortDesc: form.shortDesc,
  receivedBy: form.receivedBy,
  dateReceived: form.receivedDate,
  timeReceived: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
  file: form.file.name
}

  props.editMode ? emit('save', docPayload) : emit('add', docPayload)
  resetForm()
  emit('close')
}

function inputClass(error) {
  return `border rounded px-3 py-2 w-full ${error ? 'border-red-500' : 'border-gray-300'}`
}
</script>

<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-20 z-50">
    <div class="bg-white rounded shadow-lg w-[900px] max-w-full p-10 relative">
      <ArrowLeft 
        @click="$emit('close')"
        class="absolute left-6 top-6 text-black hover:text-blue-700 cursor-pointer w-6 h-6 z-50"
      />
      <form @submit.prevent="submitDoc" autocomplete="off">
        <div class="text-xl font-bold mb-1 mt-2">{{ props.editMode ? 'Edit Document' : 'Incoming Document' }}</div>
        <div class="border-b mb-2"></div>

        <!-- Date + Subject -->
        <div class="flex gap-6 mb-4">
          <div class="flex-1">
            <label class="block text-sm mb-1">Date</label>
            <input type="date" v-model="form.date" :class="inputClass(errors.date)" />
            <p v-if="errors.date" class="text-sm text-red-500 mt-1">{{ errors.date }}</p>
          </div>
          <div class="flex-1">
            <label class="block text-sm mb-1">Subject</label>
            <input type="text" v-model="form.subject" :class="inputClass(errors.subject)" />
            <p v-if="errors.subject" class="text-sm text-red-500 mt-1">{{ errors.subject }}</p>
          </div>
        </div>

        <!-- From Department/Division -->
        <div class="font-bold mb-1">Department Information</div>
        <div class="border-b mb-2"></div>
        <div class="flex gap-6 mb-4">
          <div class="flex-1">
            <label class="block text-sm mb-1">From Department</label>
            <select v-model="form.fromDept" :class="inputClass(errors.fromDept)">
              <option value="">--select Department--</option>
              <option v-for="d in departments" :key="d">{{ d }}</option>
            </select>
            <p v-if="errors.fromDept" class="text-sm text-red-500 mt-1">{{ errors.fromDept }}</p>
          </div>
          <div class="flex-1">
            <label class="block text-sm mb-1">From Division</label>
            <select v-model="form.fromDiv" :class="inputClass(errors.fromDiv)">
              <option value="">--select Division--</option>
              <option v-for="d in divisions" :key="d">{{ d }}</option>
            </select>
            <p v-if="errors.fromDiv" class="text-sm text-red-500 mt-1">{{ errors.fromDiv }}</p>
          </div>
          <div class="flex-1">
            <label class="block text-sm mb-1">Memo Code</label>
            <input type="text" v-model="form.memoCode" class="border rounded px-3 py-2 w-full" />
          </div>
        </div>

        <!-- To Department/Division -->
        <div class="font-bold mb-1">Destination Information</div>
        <div class="border-b mb-2"></div>
        <div class="flex gap-6 mb-4">
          <div class="flex-1">
            <label class="block text-sm mb-1">To Department</label>
            <select v-model="form.toDept" :class="inputClass(errors.toDept)">
              <option value="">--select Department--</option>
              <option v-for="d in departments" :key="d">{{ d }}</option>
            </select>
            <p v-if="errors.toDept" class="text-sm text-red-500 mt-1">{{ errors.toDept }}</p>
          </div>
          <div class="flex-1">
            <label class="block text-sm mb-1">To Division</label>
            <select v-model="form.toDiv" :class="inputClass(errors.toDiv)">
              <option value="">--select Division--</option>
              <option v-for="d in divisions" :key="d">{{ d }}</option>
            </select>
            <p v-if="errors.toDiv" class="text-sm text-red-500 mt-1">{{ errors.toDiv }}</p>
          </div>
          <div class="flex-1">
            <label class="block text-sm mb-1">Tracking Number</label>
            <input type="text" v-model="form.tracking" class="border rounded px-3 py-2 w-full" />
          </div>
        </div>

        <!-- Details -->
        <div class="font-bold mb-1">Documents Details</div>
        <div class="border-b mb-2"></div>
        <div class="flex gap-6 mb-4">
          <div class="flex-1">
            <label class="block text-sm mb-1">Short Description</label>
            <input type="text" v-model="form.shortDesc" :class="inputClass(errors.shortDesc)" />
            <p v-if="errors.shortDesc" class="text-sm text-red-500 mt-1">{{ errors.shortDesc }}</p>
          </div>
          <div class="flex-1">
            <label class="block text-sm mb-1">Received By</label>
            <input type="text" v-model="form.receivedBy" :class="inputClass(errors.receivedBy)" />
            <p v-if="errors.receivedBy" class="text-sm text-red-500 mt-1">{{ errors.receivedBy }}</p>
          </div>
          <div class="flex-1">
            <label class="block text-sm mb-1">Date</label>
            <input type="date" v-model="form.receivedDate" :class="inputClass(errors.receivedDate)" />
            <p v-if="errors.receivedDate" class="text-sm text-red-500 mt-1">{{ errors.receivedDate }}</p>
          </div>
        </div>

        <!-- File -->
        <div class="font-bold mb-1">File Attachment</div>
        <div class="border-b mb-2"></div>
        <div class="mb-6">
          <input
            ref="fileInput"
            type="file"
            accept="application/pdf"
            @change="onFileChange"
            :class="inputClass(errors.file)"
          />
          <span v-if="form.file" class="ml-2 text-green-700">{{ form.file.name }}</span>
          <p v-if="errors.file" class="text-sm text-red-500 mt-1">{{ errors.file }}</p>
        </div>

        <div class="pl-80">
          <button type="submit" class="btn-primary">
            {{ props.editMode ? 'Save Changes' : 'Submit Document' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
