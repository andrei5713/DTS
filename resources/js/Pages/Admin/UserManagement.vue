<template>
  <div class="space-y-6">
    <!-- Search (removed Add User button) -->
    <div class="flex justify-between items-center">
      <div class="flex-1 max-w-sm">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search users..."
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <!-- Removed Add User button -->
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">All Users</h3>
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
                <select
                  v-model="user.role"
                  @change="updateUserRole(user)"
                  class="text-sm border border-gray-300 rounded px-2 py-1"
                  :disabled="user.role === 'admin'"
                >
                  <option value="user">User</option>
                  <option value="department">Department</option>
                  <option value="admin">Admin</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ user.unit?.full_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="statusColors[user.role]">
                  {{ user.role === 'pending' ? 'Pending' : user.role }}
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
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useDeleteAlert } from '@/composables/useDeleteAlert'

const { confirmDelete } = useDeleteAlert()

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
})

const users = ref(props.users)
const searchQuery = ref('')

const statusColors = {
  admin: 'bg-red-100 text-red-800',
  department: 'bg-blue-100 text-blue-800',
  user: 'bg-green-100 text-green-800',
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

const updateUserRole = (user) => {
  router.put(`/admin/users/${user.id}`, {
    role: user.role,
  })
}

const deleteUser = async (user) => {
  const confirmed = await confirmDelete()
  if (confirmed) {
    router.delete(`/admin/users/${user.id}`)
  }
}

//
</script>