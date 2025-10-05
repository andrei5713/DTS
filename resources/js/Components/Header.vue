<template>
    <header class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center space-x-4">
                <img src="@/Images/NFA.svg" class="w-12 h-12">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">NATIONAL FOOD AUTHORITY</h1>
                    <p class="text-sm text-gray-600">DOCUMENT TRACKING SYSTEM</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Unit Display -->
                <div v-if="userUnit" class="flex items-center space-x-2 px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                    <span>{{ userUnit }}</span>
                </div>
                
                <!-- Role Display -->
                <div class="flex items-center space-x-2 px-3 py-1 rounded-full text-sm font-medium" :class="roleDisplayClass">
                    <component :is="roleIcon" class="w-4 h-4" />
                    <span>{{ roleDisplayText }}</span>
                </div>

                <div class="relative">
                    <button @click="toggleNotifications" class="relative">
                        <Bell class="w-6 h-6" />
                        <span v-if="badgeUnreadCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ badgeUnreadCount > 9 ? '9+' : badgeUnreadCount }}
                        </span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl ring-1 ring-gray-200 z-20 max-h-96 overflow-y-auto">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                                <button v-if="hasUnreadNotifications && !isMarkingAllAsRead" @click.stop="markAllAsRead" class="text-sm text-blue-600 hover:text-blue-800">
                                    Mark all as read
                                </button>
                                <span v-if="isMarkingAllAsRead" class="text-sm text-gray-500">
                                    Marking...
                                </span>
                            </div>
                        </div>
                        
                        <div v-if="notifications.length === 0" class="p-4 text-center text-gray-500">
                            No notifications
                        </div>
                        
                        <div v-else class="divide-y divide-gray-200">
                            <div v-for="notification in notifications" :key="notification.id" 
                                 @click.stop="handleNotificationClick(notification)"
                                 data-notification-item
                                 :class="[
                                     'p-4 hover:bg-gray-50 cursor-pointer transition-colors',
                                     notification.read ? 'bg-gray-50' : 'bg-white',
                                     notification._isMarkingAsRead ? 'opacity-75 pointer-events-none' : ''
                                 ]">
                                <div class="flex items-start space-x-3">
                                    <div :class="[
                                        'w-2 h-2 rounded-full mt-2 flex-shrink-0',
                                        notification.read ? 'bg-gray-300' : 'bg-blue-500'
                                    ]"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                                            <div v-if="notification._isMarkingAsRead" class="flex items-center text-xs text-gray-500">
                                                <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-blue-500 mr-1"></div>
                                                Processing...
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ formatDate(notification.created_at) }}</p>
                                        <div v-if="notification.type === 'document_received' || notification.type === 'document_forwarded' || notification.type === 'document_received_incoming' || notification.type === 'document_forwarded_received'" class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Click to view document
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <!-- toggle button -->
                    <button @click.stop="toggleDropdown" class="flex w-fit items-center gap-2 profile-btn">
                        <span class="font-semibold">{{ username }}</span>
                        <ChevronDown class="w-4 h-4" />
                    </button>
                    <div v-if="isShowLogin" ref="dropdownRef"
                        class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-xl ring-1 ring-gray-200 z-10"
                        @click.stop>
                        <Link :href="route('logout')" method="post" as="button" @click="clearNotificationsState"
                            class="block w-full text-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log
                        out
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- PDF Viewer Modal -->
    <EnhancedPdfViewer
      v-if="showPdfModal" 
      :document="pdfDocument" 
      :pdf-url="pdfUrl" 
      @close="closePdfModal" 
    />
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Bell, ChevronDown, Shield, Users, User } from 'lucide-vue-next'
import EnhancedPdfViewer from '@/Components/EnhancedPdfViewer.vue'

const isShowLogin = ref(false)
const dropdownRef = ref(null)
const showNotifications = ref(false)
const notifications = ref([])
const unreadCount = ref(0) // server-sourced unread count
const isMarkingAllAsRead = ref(false)
const isProcessingNotification = ref(false)
const showPdfModal = ref(false)
const pdfDocument = ref(null)
const pdfUrl = ref('')

const toggleDropdown = () => {
    isShowLogin.value = !isShowLogin.value
}

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value
    // The watcher will handle fetching notifications when dropdown opens
}

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isShowLogin.value = false
    }
    // Don't close notifications dropdown if PDF modal is open or if clicking on notification items or mark all as read button
    if (showNotifications.value && 
        !event.target.closest('.relative') && 
        !showPdfModal.value &&
        !event.target.closest('[data-notification-item]') &&
        !event.target.closest('button[class*="text-blue-600"]')) {
        showNotifications.value = false
    }
}

const logout = () => {
    router.post(route('logout'))
}

// Notification functions
const refreshNotificationsIfNeeded = () => {
    // Only refresh if dropdown is open and we're not processing
    if (showNotifications.value && !isProcessingNotification.value) {
        console.log('Refreshing notifications due to dropdown being open')
        fetchNotifications()
    }
}

// Add a watcher to refresh notifications when dropdown opens
watch(showNotifications, (newValue) => {
    if (newValue && !isProcessingNotification.value) {
        // Always fetch fresh data when dropdown opens to ensure we have the latest read status
        // This is especially important after login/logout cycles
        setTimeout(() => {
            fetchNotifications(true) // Force refresh to get latest data from server
        }, 100)
    }
})

const hasUserInteractedWithNotifications = ref(false)

// Computed property to check if there are any unread notifications
const hasUnreadNotifications = computed(() => {
    return notifications.value.some(notification => !notification.read)
})

// Badge prefers server unread count; falls back to local computed if needed
const badgeUnreadCount = computed(() => {
    if (typeof unreadCount.value === 'number' && unreadCount.value >= 0) {
        return unreadCount.value
    }
    return notifications.value.filter(notification => !notification.read).length
})

const fetchNotifications = async (forceRefresh = false) => {
    // Don't fetch if we're currently processing a notification
    if (isProcessingNotification.value) {
        console.log('Skipping notification fetch - processing notification')
        return
    }
    
    // Always fetch fresh data from server on initial load or when forced
    // This ensures we get the correct read status from the database
    if (!forceRefresh && hasUserInteractedWithNotifications.value && notifications.value.length > 0) {
        console.log('Skipping notification fetch - user has interacted with notifications and we have local data')
        return
    }
    
    try {
        console.log('Fetching notifications...')
        const response = await fetch('/api/notifications', {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        if (response.ok) {
            const data = await response.json()
            console.log('Fetched notifications data:', data)
            notifications.value = data.notifications
            unreadCount.value = data.unread_count ?? notifications.value.filter(n => !n.read).length
            console.log('Set notifications:', notifications.value)
            console.log('Server unread count:', unreadCount.value)
            
            // Clear locally modified flags when fetching fresh data
            notifications.value.forEach(notification => {
                delete notification._locallyMarkedAsRead
            })
        } else {
            console.error('Failed to fetch notifications:', response.status, response.statusText)
        }
    } catch (error) {
        console.error('Error fetching notifications:', error)
    }
}

const markAsRead = async (notification, retryCount = 0) => {
    if (notification.read) {
        console.log('Notification already marked as read:', notification.id)
        return true
    }
    
    console.log(`Marking notification as read (attempt ${retryCount + 1}):`, notification.id)
    
    try {
        // Get CSRF token first
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        
        if (!csrfToken) {
            console.error('CSRF token not found')
            throw new Error('CSRF token not found')
        }
        
        const response = await fetch(`/api/notifications/${notification.id}/read`, {
            method: 'POST',
            headers: { 
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        
        if (response.ok) {
            console.log('Notification marked as read successfully:', notification.id)
            // Update UI immediately after successful backend call
            notification.read = true
            notification.read_at = new Date().toISOString()
            unreadCount.value = Math.max(0, unreadCount.value - 1)
            console.log('UI updated - unread count:', unreadCount.value)
            return true
        } else {
            const errorText = await response.text()
            console.error('Failed to mark notification as read:', response.status, response.statusText, errorText)
            
            // Retry up to 2 times for server errors
            if (response.status >= 500 && retryCount < 2) {
                console.log(`Retrying mark as read (attempt ${retryCount + 2})`)
                await new Promise(resolve => setTimeout(resolve, 1000 * (retryCount + 1))) // Exponential backoff
                return await markAsRead(notification, retryCount + 1)
            }
            
            throw new Error(`Failed to mark notification as read: ${response.status} ${response.statusText}`)
        }
    } catch (error) {
        console.error('Error marking notification as read:', error)
        
        // Retry up to 2 times for network errors
        if (retryCount < 2 && (error.name === 'TypeError' || error.message.includes('fetch'))) {
            console.log(`Retrying mark as read due to network error (attempt ${retryCount + 2})`)
            await new Promise(resolve => setTimeout(resolve, 1000 * (retryCount + 1))) // Exponential backoff
            return await markAsRead(notification, retryCount + 1)
        }
        
        throw error
    }
}

const markAllAsRead = async () => {
    if (isMarkingAllAsRead.value) {
        console.log('Already marking all as read, ignoring duplicate call')
        return
    }
    
    // Mark that user has interacted with notifications
    hasUserInteractedWithNotifications.value = true
    
    console.log('Mark all as read clicked')
    console.log('Current notifications:', notifications.value)
    console.log('Current unread count:', unreadCount.value)
    
    isMarkingAllAsRead.value = true
    
    try {
        // Get CSRF token first
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        console.log('CSRF token found:', !!csrfToken)
        
        if (!csrfToken) {
            console.error('CSRF token not found')
            isMarkingAllAsRead.value = false
            return
        }
        
        console.log('Making API call to mark all as read...')
        
        // Create an AbortController for timeout
        const controller = new AbortController()
        const timeoutId = setTimeout(() => controller.abort(), 10000) // 10 second timeout
        
        const response = await fetch('/api/notifications/read-all', {
            method: 'POST',
            headers: { 
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            signal: controller.signal
        })
        
        clearTimeout(timeoutId)
        
        console.log('Mark all as read response:', response.status, response.statusText)
        
        if (response.ok) {
            const data = await response.json()
            console.log('Mark all as read success:', data)
            console.log('Updated count:', data.updated_count)
            console.log('Unread count before:', data.unread_count_before)
            
            // Update UI after successful backend call
            console.log('Updating UI after successful backend call...')
            if (notifications.value && notifications.value.length > 0) {
                notifications.value.forEach(notification => {
                    console.log('Marking notification as read:', notification.id, notification.read)
                    notification.read = true
                    notification._locallyMarkedAsRead = true // Mark as locally modified
                })
            }
            console.log('UI updated. New unread count:', actualUnreadCount.value)
            console.log('Final notifications state:', notifications.value)
        } else {
            const errorData = await response.json().catch(() => ({}))
            console.error('Mark all as read failed:', errorData)
            // Show user-friendly error message
            alert('Failed to mark all notifications as read. Please try again.')
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error)
        
        if (error.name === 'AbortError') {
            alert('Request timed out. Please check your connection and try again.')
        } else {
            // Show user-friendly error message
            alert('An error occurred while marking notifications as read. Please try again.')
        }
    } finally {
        isMarkingAllAsRead.value = false
    }
}

const handleNotificationClick = async (notification) => {
    console.log('Notification clicked:', notification)
    
    // Mark that user has interacted with notifications
    hasUserInteractedWithNotifications.value = true
    
    // Prevent duplicate clicks
    if (notification._isMarkingAsRead || isProcessingNotification.value) {
        console.log('Notification is already being processed, ignoring duplicate click')
        return
    }
    
    // Set processing flag
    isProcessingNotification.value = true
    notification._isMarkingAsRead = true
    
    // Mark as read with retry mechanism
    let markAsReadSuccess = false
    try {
        markAsReadSuccess = await markAsRead(notification)
        console.log('Notification marked as read successfully:', markAsReadSuccess)
        
        // Update local state immediately after successful backend call
        if (markAsReadSuccess) {
            notification.read = true
            notification.read_at = new Date().toISOString()
            notification._locallyMarkedAsRead = true // Mark as locally modified
            console.log('Local state updated - notification marked as read')
            // Re-fetch unread count shortly after to reconcile the badge
            setTimeout(() => fetchNotifications(true), 300)
        }
    } catch (error) {
        console.error('Failed to mark notification as read after retries:', error)
        alert('Could not mark notification as read. Please try again.')
    } finally {
        // Clear the flags
        notification._isMarkingAsRead = false
        isProcessingNotification.value = false
    }
    
    // If it's a document notification, open the document viewer
    if (notification.type === 'document_received' || notification.type === 'document_forwarded' || notification.type === 'document_received_incoming' || notification.type === 'document_forwarded_received') {
        const documentId = notification.data?.document_id
        console.log('Document ID:', documentId)
        if (documentId) {
            try {
                await openDocumentViewer(documentId)
            } catch (error) {
                console.error('Error opening document viewer:', error)
                // Notification is already marked as read above, so we don't need to do anything else
            }
        }
    }
    
    // Don't refresh notifications immediately to avoid overriding the local state
    // The notification will be refreshed when the dropdown is reopened
    console.log('Notification processing completed, local state updated')
}

const openDocumentViewer = async (documentId) => {
    try {
        console.log('Opening document viewer for ID:', documentId)
        
        // Fetch document details
        const response = await fetch(`/api/documents/${documentId}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        
        console.log('Document API response status:', response.status)
        
        if (response.ok) {
            const document = await response.json()
            console.log('Document fetched:', document)
            
            // Show PDF modal
            pdfDocument.value = document
            if (document.file_path) {
                pdfUrl.value = `/storage/${document.file_path}`
                console.log('PDF URL set:', pdfUrl.value)
            } else {
                pdfUrl.value = ""
                console.log('No file path found')
            }
            showPdfModal.value = true
            console.log('PDF modal should be visible now')
            return true
        } else {
            const errorText = await response.text()
            console.error('Error fetching document:', response.status, errorText)
            alert('Error loading document: ' + response.statusText)
            return false
        }
    } catch (error) {
        console.error('Error opening document viewer:', error)
        alert('Error loading document: ' + error.message)
        return false
    }
}

const closePdfModal = () => {
    showPdfModal.value = false
    pdfUrl.value = ""
    pdfDocument.value = null
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffInMinutes = Math.floor((now - date) / (1000 * 60))
    
    if (diffInMinutes < 1) return 'Just now'
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`
    return date.toLocaleDateString()
}

// Utility to clear local notification-related UI state
const clearNotificationsState = () => {
    showNotifications.value = false
    isProcessingNotification.value = false
    isMarkingAllAsRead.value = false
    hasUserInteractedWithNotifications.value = false
    notifications.value = []
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    // Always fetch fresh notifications on initial load to ensure correct read status
    fetchNotifications(true)
    
    // Refresh notifications every 30 seconds
    setInterval(fetchNotifications, 30000)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})

const page = usePage()
const username = page.props.auth?.user?.username || 'Guest'
const userRole = page.props.auth?.user?.role
const userUnit = page.props.auth?.user?.unit?.full_name || null

// Reset notifications when the authenticated user changes (handles logout/login cycles)
watch(() => page.props.auth?.user?.id, (newUserId, oldUserId) => {
    if (newUserId !== oldUserId) {
        clearNotificationsState()
        if (newUserId) {
            // New login: fetch fresh notifications from server
            fetchNotifications(true)
        }
    }
})

const roleDisplayText = computed(() => {
  switch (userRole) {
    case 'admin':
      return 'Admin'
    case 'department':
      return 'Department Head'
    case 'encoder':
      return 'Encoder'
    case 'viewer':
      return 'Viewer'
    case 'pending':
      return 'Pending'
    default:
      return 'Guest'
  }
})

const roleIcon = computed(() => {
  switch (userRole) {
    case 'admin':
      return Shield
    case 'department':
      return Shield
    case 'encoder':
      return Users
    case 'viewer':
      return User
    case 'pending':
      return User
    default:
      return User
  }
})

const roleDisplayClass = computed(() => {
  switch (userRole) {
    case 'admin':
      return 'bg-red-100 text-red-800'
    case 'department':
      return 'bg-purple-100 text-purple-800'
    case 'encoder':
      return 'bg-blue-100 text-blue-800'
    case 'viewer':
      return 'bg-green-100 text-green-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
})

const isAdmin = computed(() => userRole === 'admin')
</script>
