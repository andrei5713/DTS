<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Filter by Department'
    }
})

const emit = defineEmits(['update:modelValue'])

const departments = [
    'AO',
    'ODA',
    'OAAFA',
    'OAAO',
    'AGSD',
    'CPMSD',
    'FD',
    'IAD',
    'OCD',
]

const isOpen = ref(false)

function handleChange(event) {
    emit('update:modelValue', event.target.value)
}

function handleFocus() {
    isOpen.value = true
}

function handleBlur() {
    isOpen.value = false
}
</script>

<template>
    <div class="relative">
        <select
            :value="modelValue"
            @change="handleChange"
            @focus="handleFocus"
            @blur="handleBlur"
            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-700 appearance-none"
        >
            <option value="">{{ placeholder }}</option>
            <option v-for="dept in departments" :key="dept" :value="dept">
                {{ dept }}
            </option>
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg 
                class="w-4 h-4 text-gray-400 transition-transform duration-200" 
                :class="{ 'rotate-180': isOpen }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>
</template> 