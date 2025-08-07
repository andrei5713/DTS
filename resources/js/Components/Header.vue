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
                <!-- Role Display -->
                <div class="flex items-center space-x-2 px-3 py-1 rounded-full text-sm font-medium" :class="roleDisplayClass">
                    <component :is="roleIcon" class="w-4 h-4" />
                    <span>{{ roleDisplayText }}</span>
                </div>

                <button class="relative">
                    <Bell class="w-6 h-6" />
                </button>

                <div class="relative">
                    <!-- toggle button -->
                    <button @click.stop="toggleDropdown" class="flex w-fit items-center gap-2 profile-btn">
                        <span class="font-semibold">{{ username }}</span>
                        <ChevronDown class="w-4 h-4" />
                    </button>
                    <div v-if="isShowLogin" ref="dropdownRef"
                        class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-xl ring-1 ring-gray-200 z-10"
                        @click.stop>
                        <Link :href="route('logout')" method="post" as="button"
                            class="block w-full text-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log
                        out
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Bell, ChevronDown, Shield, Users, User } from 'lucide-vue-next'

const isShowLogin = ref(false)
const dropdownRef = ref(null)

const toggleDropdown = () => {
    isShowLogin.value = !isShowLogin.value
}

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isShowLogin.value = false
    }
}

const logout = () => {
    router.post(route('logout'))
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})

const page = usePage()
const username = page.props.auth?.user?.username || 'Guest'
const userRole = page.props.auth?.user?.role

const roleDisplayText = computed(() => {
  switch (userRole) {
    case 'admin':
      return 'Admin'
    case 'department_head':
      return 'Department Head'
    case 'user':
      return 'User'
    default:
      return 'Guest'
  }
})

const roleIcon = computed(() => {
  switch (userRole) {
    case 'admin':
      return Shield
    case 'department_head':
      return Users
    case 'user':
      return User
    default:
      return User
  }
})

const roleDisplayClass = computed(() => {
  switch (userRole) {
    case 'admin':
      return 'bg-red-100 text-red-800'
    case 'department_head':
      return 'bg-blue-100 text-blue-800'
    case 'user':
      return 'bg-green-100 text-green-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
})

const isAdmin = computed(() => userRole === 'admin')
</script>
