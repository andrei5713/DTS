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
import { Bell, ChevronDown } from 'lucide-vue-next'

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
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin')
</script>
