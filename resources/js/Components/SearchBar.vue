<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Search Documents'
    },
    modelValue: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const searchQuery = ref(props.modelValue)

// Watch for changes in the local searchQuery and emit them
watch(searchQuery, (newValue) => {
    emit('update:modelValue', newValue)
})

// Watch for changes in the prop and update local value
watch(() => props.modelValue, (newValue) => {
    searchQuery.value = newValue
})
</script>

<template>
    <div class="relative">
        <input
            :id="'search-' + Math.random().toString(36).substr(2, 9)"
            v-model="searchQuery"
            type="text"
            :placeholder="placeholder"
            class="px-4 py-3 pl-10 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
        />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>
</template> 