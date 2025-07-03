<template>
    <div :class="[
        'px-4 py-2 rounded-md flex items-center space-x-3 cursor-pointer transition-colors duration-200',
        isActive
            ? 'bg-zinc-800 text-white'
            : 'text-white-700 hover:bg-zinc-100'
    ]" @click="$emit('click')">
        <component :is="item.icon" class="w-5 h-5" />
        <span class="font-medium">{{ item.label }}</span>
    </div>
</template>

<script setup>
const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    isActive: {
        type: Boolean,
        default: false
    }
})

defineEmits(['click'])
</script>