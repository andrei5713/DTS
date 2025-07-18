<template>
    <div>
        <!-- Parent Item -->
        <div :class="[
            'px-4 py-2 rounded-md flex justify-between items-center cursor-pointer transition-colors duration-200',
            isActive ? 'bg-zinc-800 text-white' : 'text-gray-700 hover:bg-zinc-100'
        ]">
            <!-- Left: Icon + Label (navigates on click) -->
            <div class="flex items-center space-x-3 cursor-pointer" @click="handleParentClick">
                <component :is="item.icon" class="w-5 h-5" />
                <span class="font-medium">{{ item.label }}</span>
            </div>

            <!-- Right: Chevron (toggles dropdown only) -->
            <span v-if="item.children" class="text-xs" @click.stop="toggleDropdown">
                <ChevronDown class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" />
            </span>
        </div>

        <!-- Children Dropdown -->
        <div v-if="item.children && isOpen" class="ml-6 mt-1 space-y-1">
            <div v-for="child in item.children" :key="child.id" :class="[
                'px-4 py-2 rounded-md cursor-pointer transition-colors duration-200',
                url.includes(child.id) ? 'bg-zinc-800 text-white' : 'text-gray-700 hover:bg-zinc-100'
            ]" @click="$emit('click', child.id)">
                {{ child.label }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import { usePage } from '@inertiajs/vue3'

const { url } = usePage()

const props = defineProps({
    item: Object,
    isActive: Boolean,
})

const emit = defineEmits(['click'])

const isOpen = ref(false)

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
}

// Auto-open if any child is active
onMounted(() => {
    if (props.item.children?.some(child => url.includes(child.id))) {
        isOpen.value = true
    }
})

const handleParentClick = () => {
    if (!props.item.children) {
        emit('click', props.item.id)
    } else {
        // If parent has children, still navigate if needed
        emit('click', props.item.id)
    }
}

</script>