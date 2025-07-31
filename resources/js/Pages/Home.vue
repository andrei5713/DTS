<template>
  <div class="min-h-full p-4">
    <Header />
    
    <!-- Status Message -->
    <div v-if="page.props.status" class="mt-4 max-w-7xl mx-auto">
      <div class="bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded">
        {{ page.props.status }}
      </div>
    </div>
    
    <div v-if="userRole === 'pending'" class="mt-10 flex flex-col items-center">
      <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-6 py-4 rounded max-w-2xl text-center">
        <h3 class="text-lg font-semibold mb-2">Account Pending Approval</h3>
        <p>Your account is currently awaiting admin approval. You will be able to access the system once an administrator reviews and approves your account.</p>
        <p class="mt-2 text-sm">Please check back later or contact your administrator.</p>
      </div>
    </div>
    <div v-else-if="userRole === 'admin'">
      <div class="mt-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Admin Panel</h2>
        
        <Tabs v-model="activeTab" :tabs="adminTabs">
          <template #default="{ activeTab }">
            <div v-if="activeTab === 'dashboard'">
              <AdminDashboard :stats="page.props.stats || {}" :recent_users="page.props.recent_users || []" />
            </div>
            <div v-else-if="activeTab === 'users'">
              <UserManagement :users="page.props.users || []" />
            </div>
            <div v-else-if="activeTab === 'pending'">
              <PendingUsers :pending-users="page.props.pendingUsers || []" :units="page.props.units || []" />
            </div>
          </template>
        </Tabs>
      </div>
    </div>
    <div v-else>
      <div class="mt-6">
        <Tabs v-model="activeTab">
          <template #default="{ activeTab }">
            <div v-if="activeTab === 'incoming'">
              <Incoming />
            </div>
            <div v-else-if="activeTab === 'outgoing'">
              <Outgoing />
            </div>
          </template>
        </Tabs>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Header from '../Components/Header.vue'
import Tabs from '../Components/Tabs.vue'
import Incoming from './Incoming.vue'
import Outgoing from './Outgoing.vue'
import AdminDashboard from './Admin/AdminDashboard.vue'
import UserManagement from './Admin/UserManagement.vue'
import PendingUsers from './Admin/PendingUsers.vue'

const activeTab = ref('dashboard')
const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role)

const adminTabs = [
  { label: 'Dashboard', value: 'dashboard' },
  { label: 'User Management', value: 'users' },
  { label: 'Pending Users', value: 'pending' },
]
</script>