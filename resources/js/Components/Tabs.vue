<template>
  <div>
    <div class="flex items-end mb-0 relative space-x-1">
      <template v-for="(tab, i) in tabs" :key="tab.value">
        <button
          @click="setActiveTab(tab.value)"
          :class="[
            'relative px-10 pt-4 pb-3 text-2xl font-bold focus:outline-none',
            'transition-colors duration-150',
            'rounded-t-[10px] border border-b-0',
            activeTab === tab.value
              ? [
                  'bg-white z-20',
                  tab.value === 'incoming' ? 'text-red-600' : '',
                  tab.value === 'outgoing' ? 'text-orange-500' : '',
                ]
              : [
                  'bg-gray-100 z-10',
                  tab.value === 'incoming' ? 'text-red-400 border-gray-200' : '',
                  tab.value === 'outgoing' ? 'text-orange-300 border-gray-200' : '',
                ],
          ]"
        >
          <span class="block" style="line-height: 1.5;">{{ tab.label }}</span>
        </button>
      </template>
      <div class="flex-1 h-[1px] bg-gray-300 absolute bottom-0 left-full" style="width: 100vw;"></div>
    </div>
    <div class="border-x border-b border-gray-300 bg-white rounded-b-lg -mt-px z-0 relative" style="border-top: none;">
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

const activeTab = ref(props.modelValue)

function setActiveTab(tab) {
  activeTab.value = tab
  emit('update:activeTab', tab)
}

watch(() => props.modelValue, (val) => {
  activeTab.value = val
})
</script>

<style scoped>
button:first-child {
  border-left-width: 1px !important;
}
</style> 