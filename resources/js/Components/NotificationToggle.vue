<template>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-medium text-gray-800">{{ label }}</h3>
            <p class="text-sm text-gray-500">{{ description }}</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer" :checked="modelValue"
                @change="$emit('update:modelValue', $event.target.checked)" />
            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-600 transition"></div>
            <div
                class="absolute left-0.5 top-0.5 w-5 h-5 bg-white border rounded-full transition-transform duration-200 peer-checked:translate-x-full">
            </div>
        </label>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    description: String,
    modelValue: Boolean,
})

defineEmits(['update:modelValue'])
</script>