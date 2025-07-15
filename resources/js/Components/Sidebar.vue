<script setup>
import { LayoutDashboard, FileInput, FileOutput, Navigation, HandCoins, Settings, LogOut } from 'lucide-vue-next';
import SidebarItem from './SidebarItem.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    activeSection: {
        type: String,
        required: true
    }
})

const emit = defineEmits(['section-change'])

const menuItems = [
    {
        id: 'dashboard',
        label: 'Dashboard',
        icon: LayoutDashboard
    },
    {
        id: 'incoming',
        label: 'Incoming',
        icon: FileInput
    },
    {
        id: 'outgoing',
        label: 'Outgoing',
        icon: FileOutput
    },
    {
        id: 'travel',
        label: 'Travel',
        icon: Navigation
    },
    {
        id: 'bur',
        label: 'BUR',
        icon: HandCoins
    },
    {
        id: 'settings',
        label: 'Settings',
        icon: Settings
    },
]

const handleItemClick = (sectionId) => {
    if (sectionId === 'signout') {
        router.post('/logout')
        return
    }

    // Navigate to section via Inertia route
    router.visit(`/${sectionId}`)
}
</script>

<template>
    <aside class="w-64 bg-white shadow-sm min-h-screen rounded-lg p-4">
        <nav class="p-4 space-y-2 ">
            <SidebarItem v-for="item in menuItems" :key="item.id" :item="item" :is-active="activeSection === item.id"
                @click="() => handleItemClick(item.id)" />
        </nav>
    </aside>
</template>
