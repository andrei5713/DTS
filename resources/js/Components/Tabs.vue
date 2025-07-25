<template>
    <div class="bg-gray-200 relative">
        <!-- Horizontal tabs aligned to the left -->
        <div class="h-12 flex items-center space-x-4 shadow-sm">
            <div
                v-for="(tab, idx) in tabs"
                :key="tab.value"
                class="relative cursor-pointer transition-all duration-150"
                :class="[
                    'select-none px-5 py-2 rounded-t-md font-semibold',
                    tab.value === activeTab 
                        ? 'bg-white shadow text-gray-800 z-10' 
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-50 hover:text-gray-800 shadow-sm border border-gray-300'
                ]"
                @click="setActiveTab(tab.value)"
            >
                {{ tab.label }}
            </div>
        </div>

        <!-- Tab Content -->
        <div class="bg-white rounded-b-lg shadow-lg px-4 py-4">
            <slot :active-tab="activeTab" />
        </div>
    </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: 'incoming',
    },
})
const emit = defineEmits(['update:activeTab'])

const tabs = [
    { label: 'Incoming', value: 'incoming' },
    { label: 'Outgoing', value: 'outgoing' },
    { label: 'Archive', value: 'archive' },
]

const activeTab = ref(props.modelValue)

function setActiveTab(tab) {
    activeTab.value = tab
    emit('update:activeTab', tab)
}

watch(() => props.modelValue, (val) => {
    activeTab.value = val
})
</script>