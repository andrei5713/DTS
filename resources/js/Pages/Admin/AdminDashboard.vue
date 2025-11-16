<template>
  <div class="space-y-8">
    <!-- Page Header with proper spacing -->
    <div class="pt-8 pb-4">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Dashboard Overview</h1>
      <p class="text-gray-600">System statistics and recent user activity</p>
    </div>

    <!-- Stats Cards with modern design -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Total Users Card -->
      <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-5 rounded-full translate-y-12 -translate-x-12"></div>
        <div class="relative z-10">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-white bg-opacity-20 backdrop-blur-sm">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-blue-100 opacity-90">Total Users</p>
              <p class="text-3xl font-bold text-white">{{ stats.total_users }}</p>
            </div>
          </div>
          <div class="flex items-center text-blue-100 text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            <span>Active accounts</span>
          </div>
        </div>
      </div>

      <!-- Pending Users Card -->
      <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-5 rounded-full translate-y-12 -translate-x-12"></div>
        <div class="relative z-10">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-white bg-opacity-20 backdrop-blur-sm">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-amber-100 opacity-90">Pending Users</p>
              <p class="text-3xl font-bold text-white">{{ stats.pending_users }}</p>
            </div>
          </div>
          <div class="flex items-center text-amber-100 text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Awaiting approval</span>
          </div>
        </div>
      </div>

      <!-- Active Units Card -->
      <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-green-500 to-teal-500 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-5 rounded-full translate-y-12 -translate-x-12"></div>
        <div class="relative z-10">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-white bg-opacity-20 backdrop-blur-sm">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-emerald-100 opacity-90">Active Units</p>
              <p class="text-3xl font-bold text-white">{{ stats.active_units }}</p>
            </div>
          </div>
          <div class="flex items-center text-emerald-100 text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Operational departments</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Users with modern design -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
      <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-semibold text-gray-900">Recent Users</h3>
            <p class="text-sm text-gray-600 mt-1">Latest user registrations and updates</p>
          </div>
          <div class="p-3 rounded-xl bg-blue-100 text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
        </div>
      </div>
              <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Role</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Unit</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Joined</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="user in recent_users" :key="user.id" class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-semibold text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.username }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full shadow-sm" :class="roleColors[user.role]">
                    {{ user.role === 'encoder' ? 'Encoder' : user.role === 'viewer' ? 'Viewer' : user.role === 'pending' ? 'Pending' : user.role }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ user.unit?.full_name || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ new Date(user.created_at).toLocaleDateString() }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
    </div>
    
    <!-- Bottom spacing -->
    <div class="pb-8"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      total_users: 0,
      pending_users: 0,
      active_units: 0,
    }),
  },
  recent_users: {
    type: Array,
    default: () => [],
  },
})

const roleColors = {
  admin: 'bg-red-100 text-red-800',
  encoder: 'bg-blue-100 text-blue-800',
  viewer: 'bg-green-100 text-green-800',
  clerk: 'bg-purple-100 text-purple-800',
  pending: 'bg-yellow-100 text-yellow-800',
}
</script>