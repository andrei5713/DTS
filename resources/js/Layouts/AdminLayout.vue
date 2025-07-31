<script setup>
import { ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const props = defineProps({
    title: String,
})

const page = usePage()
const user = page.props.auth?.user

const navigation = [
    { name: 'Dashboard', href: route('admin.dashboard'), icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z' },
    { name: 'User Management', href: route('admin.users.index'), icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z' },
    { name: 'Unit Management', href: route('admin.units.index'), icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
]

const isSidebarOpen = ref(false)

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out" :class="{ '-translate-x-full': !isSidebarOpen }">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-900">Admin Panel</h1>
                <button @click="isSidebarOpen = false" class="lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <nav class="mt-6">
                <div class="px-3 space-y-1">
                    <Link v-for="item in navigation" :key="item.name" :href="item.href" 
                          class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors"
                          :class="$page.url === item.href ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'">
                        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                        </svg>
                        {{ item.name }}
                    </Link>
                </div>
            </nav>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top bar -->
            <div class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <button @click="isSidebarOpen = true" class="lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-700">
                            Welcome, {{ user?.name }}
                        </div>
                        <Link :href="route('home')" class="text-sm text-blue-600 hover:text-blue-800">
                            Back to App
                        </Link>
                        <button @click="logout" class="text-sm text-red-600 hover:text-red-800">
                            Logout
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template> 