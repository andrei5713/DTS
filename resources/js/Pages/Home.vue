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
            <div v-else-if="userRole === 'encoder'">
      <div class="mt-6">
        <Tabs v-model="activeTab" :tabs="departmentHeadTabs">
          <template #default="{ activeTab }">
            <div>
              <div v-show="activeTab === 'outgoing'">
                <Outgoing />
              </div>
              <div v-show="activeTab === 'incoming'">
                <Incoming />
              </div>
              <div v-show="activeTab === 'archived'">
                <ArchivedDocuments />
              </div>
            </div>
          </template>
        </Tabs>
      </div>
    </div>
    <div v-else-if="userRole === 'viewer'">
      <div class="mt-6">
        <Tabs v-model="activeTab" :tabs="departmentHeadTabs">
          <template #default="{ activeTab }">
            <div>
              <div v-show="activeTab === 'outgoing'">
                <Outgoing />
              </div>
              <div v-show="activeTab === 'incoming'">
                <Incoming />
              </div>
              <div v-show="activeTab === 'archived'">
                <ArchivedDocuments />
              </div>
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
import { ref, computed, watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Header from '../Components/Header.vue'
import Tabs from '../Components/Tabs.vue'
import Incoming from './Incoming.vue'
import Outgoing from './Outgoing.vue'
import AdminDashboard from './Admin/AdminDashboard.vue'
import UserManagement from './Admin/UserManagement.vue'
import PendingUsers from './Admin/PendingUsers.vue'
import ArchivedDocuments from './DepartmentHead/ArchivedDocuments.vue'

const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role)

const adminTabs = [
  { label: 'Dashboard', value: 'dashboard' },
  { label: 'User Management', value: 'users' },
  { label: 'Pending Users', value: 'pending' },
]

const departmentHeadTabs = [
  { label: 'Outgoing', value: 'outgoing' },
  { label: 'Incoming', value: 'incoming' },
  { label: 'Archived', value: 'archived' },
]

const userTabs = [
  { label: 'Incoming', value: 'incoming' },
  { label: 'Outgoing', value: 'outgoing' },
]

function getTabKey(role) {
  if (role === 'admin') return 'activeTab_admin'
          if (role === 'encoder') return 'activeTab_encoder'
      if (role === 'viewer') return 'activeTab_viewer'
  return 'activeTab_guest'
}

const activeTab = ref('outgoing')

onMounted(() => {
  const key = getTabKey(userRole.value)
  const saved = localStorage.getItem(key)
  if (saved) {
    activeTab.value = saved
  } else {
    // Set default tab for each role
    if (userRole.value === 'admin') activeTab.value = 'dashboard'
            else if (userRole.value === 'encoder') activeTab.value = 'outgoing'
            else if (userRole.value === 'viewer') activeTab.value = 'incoming'
    else activeTab.value = 'incoming'
  }
})

watch([activeTab, userRole], ([tab, role]) => {
  const key = getTabKey(role)
  localStorage.setItem(key, tab)
})
</script>