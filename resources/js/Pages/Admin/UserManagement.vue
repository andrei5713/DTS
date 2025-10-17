<template>
  <div class="space-y-8">
    <!-- Page Header with better spacing -->
    <div class="pt-8 pb-4">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">User Management</h1>
      <p class="text-gray-600">Manage user accounts, roles, and permissions</p>
    </div>

    <!-- Search Section with improved spacing -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex justify-between items-center">
        <div class="flex-1 max-w-sm">
          <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
          <input
            id="search"
            v-model="searchQuery"
            type="text"
            placeholder="Search users..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        <!-- Removed Add User button -->
      </div>
    </div>

    <!-- Users Table with improved spacing -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="px-6 py-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">All Users</h3>
        <p class="text-sm text-gray-600 mt-1">Manage user roles and permissions</p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                <div class="text-sm text-gray-500">{{ user.username }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                  <select
                    v-model="user.role"
                    @change="updateUserRole(user)"
                    class="text-sm border border-gray-300 rounded px-2 py-1 w-28"
                    :disabled="user.role === 'admin'"
                  >
                    <option value="encoder">Encoder</option>
                    <option value="viewer">Viewer</option>
                  </select>
                  <span 
                    v-if="user.role !== user.originalRole" 
                    class="text-xs text-blue-600 font-medium"
                    title="Role changed"
                  >
                    ●
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ user.unit?.full_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="statusColors[user.role]">
                  {{ user.role === 'pending' ? 'Pending' : user.role === 'encoder' ? 'Encoder' : user.role === 'viewer' ? 'Viewer' : user.role }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  v-if="user.role !== 'admin'"
                  @click="deleteUser(user)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Removed Add User Modal -->
    
    <!-- Bottom spacing -->
    <div class="pb-8"></div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useDeleteAlert } from '@/composables/useDeleteAlert'

const { confirmDelete } = useDeleteAlert()

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
})

const users = ref(props.users.map(user => ({
  ...user,
  originalRole: user.role // Store original role for reverting changes
})))
const searchQuery = ref('')

// Watch for changes in props and update local users
watch(() => props.users, (newUsers) => {
  users.value = newUsers.map(user => ({
    ...user,
    originalRole: user.role
  }))
}, { deep: true })

const statusColors = {
  admin: 'bg-red-100 text-red-800',
  encoder: 'bg-blue-100 text-blue-800',
  viewer: 'bg-green-100 text-green-800',
  pending: 'bg-yellow-100 text-yellow-800',
}

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query) ||
    user.username.toLowerCase().includes(query)
  )
})

const updateUserRole = async (user) => {
  try {
    await router.put(`/admin/users/${user.id}`, {
      role: user.role,
    })
    // Update the original role after successful update
    user.originalRole = user.role
    // Show success feedback
    showSuccessMessage(`User role updated to ${user.role}`)
  } catch (error) {
    console.error('Error updating user role:', error)
    // Revert the change if there's an error
    user.role = user.originalRole
    showErrorMessage('Failed to update user role')
  }
}

const showSuccessMessage = (message) => {
  // Simple success message display
  const successDiv = document.createElement('div')
  successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg z-50'
  successDiv.textContent = message
  document.body.appendChild(successDiv)
  
  setTimeout(() => {
    document.body.removeChild(successDiv)
  }, 3000)
}

const showErrorMessage = (message) => {
  // Simple error message display
  const errorDiv = document.createElement('div')
  errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded shadow-lg z-50'
  errorDiv.textContent = message
  document.body.appendChild(errorDiv)
  
  setTimeout(() => {
    document.body.removeChild(errorDiv)
  }, 3000)
}

const deleteUser = async (user) => {
  const confirmed = await confirmDelete()
  if (confirmed) {
    router.delete(`/admin/users/${user.id}`)
  }
}

//
</script>