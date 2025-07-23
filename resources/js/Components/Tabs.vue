<template>
    <div class="bg-gray-200 relative">
        <div class="h-8 flex justify-evenly">
            <div v-for="(tab, idx) in tabs" :key="tab.value" :class="[
                'h-full flex items-center select-none box-border relative cursor-pointer z-10 transition-[z-index] duration-150',
                tab.value !== tabs[tabs.length - 1].value ? 'mr-[24px]' : '',
                tab.value === activeTab ? 'z-[2]' : ''
            ]" :style="{ width: tabWidth + 'px' }" @click="setActiveTab(tab.value)">
                <div class="w-full h-full flex absolute">

                    <div class="flex-1 h-full shadow-t-lg transition-colors duration-150" :class="[
                        'rounded-t-[10px]',
                        tab.value === activeTab ? 'bg-white' : 'bg-gray-200'
                    ]"></div>

                </div>
                <div class="absolute left-20 font-semibold"
                    :class="tab.value === activeTab ? 'text-dark' : 'text-dark'">
                    {{ tab.label }}
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg px-3 py-3 pt-4">
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
]

const tabWidth = ref(245)
const activeTab = ref(props.modelValue)

function setActiveTab(tab) {
    activeTab.value = tab
    emit('update:activeTab', tab)
}

watch(() => props.modelValue, (val) => {
    activeTab.value = val
})
</script>