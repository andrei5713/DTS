<!-- SideBar.vue -->
<script setup>
import { LayoutDashboard, FileInput, FileOutput, Settings } from 'lucide-vue-next';
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
    // {
    //     id: 'dashboard',
    //     label: 'Dashboard',
    //     icon: LayoutDashboard
    // },
    {
        id: 'incoming',
        label: 'Incoming',
        icon: FileInput
    },
    {
        id: 'outgoing',
        label: 'Outgoing',
        icon: FileOutput,
        children: [
            { id: 'ictsd-outgoing', label: 'ICTSD Outgoing' },
            { id: 'cpd-outgoing', label: 'CPD Outgoing' }
        ]
    },
    {
        id: 'settings',
        label: 'Settings',
        icon: Settings
    },
]

const handleItemClick = (id) => {
    if (id === 'signout') {
        router.post('/logout')
        return
    }

    router.visit(`/${id}`)
}
</script>

<template>
    <aside class="w-64 bg-white shadow-sm min-h-screen rounded-lg p-4">
        <nav class="p-4 space-y-2 ">
            <SidebarItem v-for="item in menuItems" :key="item.id" :item="item" :is-active="activeSection === item.id"
                @click="handleItemClick" />
        </nav>
    </aside>
</template>
