<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Document Responses</h3>
              <p class="text-sm text-gray-600 mt-1">{{ document?.subject }}</p>
              <p v-if="document?.tracking_code?.includes('-FWD-')" class="text-xs text-blue-600 mt-1">
                Forwarded to: {{ document?.upload_to }}
              </p>
              <p v-else-if="document?.forwarded_by" class="text-xs text-green-600 mt-1">
                Forwarded by: {{ document?.forwarded_by }}
              </p>
            </div>
            <button 
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="flex-1 flex flex-col min-h-0">
          <!-- Messages Area -->
          <div class="flex-1 overflow-y-auto p-6 space-y-4" ref="messagesContainer">
            <div v-if="loading" class="text-center py-8">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
              <p class="mt-2 text-sm text-gray-500">Loading responses...</p>
            </div>
            
            <div v-else-if="responses.length === 0" class="text-center py-8">
              <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No responses yet</h3>
              <p class="text-sm text-gray-500">Be the first to start a conversation about this document.</p>
            </div>

            <div v-else class="space-y-4">
              <div 
                v-for="response in responses" 
                :key="response.id"
                class="flex flex-col space-y-2"
              >
                <!-- Main Response -->
                <div class="flex space-x-3">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                      {{ response.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="bg-gray-50 rounded-lg p-4">
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                          <span class="font-medium text-sm text-gray-900">{{ response.user?.name }}</span>
                          <span class="text-xs text-gray-500">{{ formatDate(response.created_at) }}</span>
                        </div>
                      </div>
                      <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ response.message }}</p>
                    </div>
                  </div>
                </div>

                <!-- Replies -->
                <div v-if="response.replies && response.replies.length > 0" class="ml-11 space-y-2">
                  <div 
                    v-for="reply in response.replies" 
                    :key="reply.id"
                    class="flex space-x-3"
                  >
                    <div class="flex-shrink-0">
                      <div class="w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center text-white text-xs font-medium">
                        {{ reply.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="bg-white border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-1">
                          <div class="flex items-center space-x-2">
                            <span class="font-medium text-xs text-gray-900">{{ reply.user?.name }}</span>
                            <span class="text-xs text-gray-500">{{ formatDate(reply.created_at) }}</span>
                          </div>
                        </div>
                        <p class="text-xs text-gray-700 whitespace-pre-wrap">{{ reply.message }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Reply Button -->
                <div class="ml-11">
                  <button 
                    @click="toggleReply(response.id)"
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                  >
                    {{ showReplyForm === response.id ? 'Cancel Reply' : 'Reply' }}
                  </button>
                </div>

                <!-- Reply Form -->
                <div v-if="showReplyForm === response.id" class="ml-11 mt-2">
                  <div class="bg-white border border-gray-200 rounded-lg p-3">
                    <textarea
                      v-model="replyMessage"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                      placeholder="Write a reply..."
                    ></textarea>
                    <div class="flex justify-end space-x-2 mt-2">
                      <button 
                        @click="cancelReply"
                        class="px-3 py-1 text-xs text-gray-600 hover:text-gray-800"
                      >
                        Cancel
                      </button>
                      <button 
                        @click="sendReply(response.id)"
                        :disabled="!replyMessage.trim() || sending"
                        class="px-3 py-1 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600 disabled:opacity-50"
                      >
                        {{ sending ? 'Sending...' : 'Send Reply' }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- New Response Form -->
          <div class="border-t border-gray-200 p-6 flex-shrink-0">
            <div class="space-y-3">
              <label class="block text-sm font-medium text-gray-700">Add Response</label>
              <textarea
                v-model="newMessage"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Write your response..."
              ></textarea>
              <div class="flex justify-end space-x-3">
                <button 
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md text-sm"
                >
                  Close
                </button>
                <button 
                  @click="sendResponse"
                  :disabled="!newMessage.trim() || sending"
                  class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:opacity-50 text-sm"
                >
                  {{ sending ? 'Sending...' : 'Send Response' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  document: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close']);

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

const responses = ref([]);
const loading = ref(false);
const sending = ref(false);
const newMessage = ref('');
const replyMessage = ref('');
const showReplyForm = ref(null);
const messagesContainer = ref(null);

// Get CSRF token
function getCSRFToken() {
  if (page.props.csrf_token) {
    return page.props.csrf_token;
  }
  const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  return metaToken || '';
}

// Fetch responses when modal opens
watch(() => props.show, (newVal) => {
  if (newVal && props.document) {
    fetchResponses();
  }
});

async function fetchResponses() {
  if (!props.document) return;
  
  loading.value = true;
  try {
    const response = await fetch(`/api/documents/${props.document.id}/responses`);
    const data = await response.json();
    
    if (data.success) {
      responses.value = data.responses;
      // Scroll to bottom after loading
      nextTick(() => {
        scrollToBottom();
      });
    }
  } catch (error) {
    console.error('Error fetching responses:', error);
  } finally {
    loading.value = false;
  }
}

async function sendResponse() {
  if (!newMessage.value.trim() || !props.document) return;
  
  sending.value = true;
  try {
    const csrfToken = getCSRFToken();
    const response = await fetch(`/api/documents/${props.document.id}/responses`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        message: newMessage.value
      })
    });
    
    const data = await response.json();
    if (data.success) {
      newMessage.value = '';
      await fetchResponses(); // Refresh responses
    } else {
      console.error('Error sending response:', data.message);
    }
  } catch (error) {
    console.error('Error sending response:', error);
  } finally {
    sending.value = false;
  }
}

async function sendReply(parentResponseId) {
  if (!replyMessage.value.trim() || !props.document) return;
  
  sending.value = true;
  try {
    const csrfToken = getCSRFToken();
    const response = await fetch(`/api/documents/${props.document.id}/responses`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        message: replyMessage.value,
        parent_response_id: parentResponseId
      })
    });
    
    const data = await response.json();
    if (data.success) {
      replyMessage.value = '';
      showReplyForm.value = null;
      await fetchResponses(); // Refresh responses
    } else {
      console.error('Error sending reply:', data.message);
    }
  } catch (error) {
    console.error('Error sending reply:', error);
  } finally {
    sending.value = false;
  }
}


function toggleReply(responseId) {
  showReplyForm.value = showReplyForm.value === responseId ? null : responseId;
  if (showReplyForm.value === responseId) {
    replyMessage.value = '';
  }
}

function cancelReply() {
  showReplyForm.value = null;
  replyMessage.value = '';
}

function closeModal() {
  emit('close');
  newMessage.value = '';
  replyMessage.value = '';
  showReplyForm.value = null;
}

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleString();
}

</script>
