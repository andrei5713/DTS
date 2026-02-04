<template>
  <div 
    @click="onClick"
    :class="clickable ? 'cursor-pointer' : ''"
    class="relative inline-flex items-center justify-center"
    :title="title"
  >
    <svg 
      :width="size" 
      :height="size" 
      class="transform -rotate-90"
    >
      <!-- Background circle -->
      <circle
        :cx="center"
        :cy="center"
        :r="radius"
        :stroke="bgColor"
        :stroke-width="strokeWidth"
        fill="none"
      />
      <!-- Progress circle -->
      <circle
        :cx="center"
        :cy="center"
        :r="radius"
        :stroke="progressColor"
        :stroke-width="strokeWidth"
        fill="none"
        :stroke-dasharray="circumference"
        :stroke-dashoffset="offset"
        stroke-linecap="round"
        class="transition-all duration-300 ease-in-out"
      />
    </svg>
    <!-- Center text -->
    <div 
      class="absolute inset-0 flex flex-col items-center justify-center"
      :class="textColor"
    >
      <span v-if="props.isOverdue || displayText === 'overdue' || displayText === 'Due'" class="font-bold text-xs leading-tight">Due</span>
      <template v-else>
        <span class="font-bold text-xs leading-tight">{{ displayText }}</span>
        <span class="text-[10px] font-medium opacity-75 leading-tight">{{ Number(displayText) === 1 ? 'day' : 'days' }}</span>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  percentage: {
    type: Number,
    required: true,
    validator: (value) => value >= 0 && value <= 100
  },
  size: {
    type: Number,
    default: 48
  },
  strokeWidth: {
    type: Number,
    default: 4
  },
  displayText: {
    type: [String, Number],
    default: ''
  },
  isOverdue: {
    type: Boolean,
    default: false
  },
  isComplied: {
    type: Boolean,
    default: false
  },
  clickable: {
    type: Boolean,
    default: true
  },
  title: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['click'])

const center = computed(() => props.size / 2)
const radius = computed(() => (props.size - props.strokeWidth) / 2)
const circumference = computed(() => 2 * Math.PI * radius.value)
const offset = computed(() => {
  // When overdue, show 100% filled circle
  if (props.isOverdue) {
    return 0
  }
  const progress = props.percentage / 100
  return circumference.value * (1 - progress)
})

const bgColor = computed(() => {
  if (props.isComplied) {
    return '#f3f4f6' // gray-100 for complied
  }
  return props.isOverdue ? '#fee2e2' : '#dcfce7' // red-100 for overdue, green-100 for normal
})

const progressColor = computed(() => {
  if (props.isComplied) {
    return '#9ca3af' // gray-400 - gray for complied
  }
  if (props.isOverdue) {
    return '#ef4444' // red-500 - full circle when overdue
  }
  // Green for all percentages above 0% (not overdue)
  return '#22c55e' // green-500
})

const textColor = computed(() => {
  if (props.isComplied) {
    return 'text-gray-600'
  }
  if (props.isOverdue) {
    return 'text-red-600'
  }
  // Green for all percentages above 0% (not overdue)
  return 'text-green-600'
})

const onClick = () => {
  if (props.clickable) {
    emit('click')
  }
}
</script>

