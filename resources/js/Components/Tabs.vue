<template>
  <div class="nav-tabs-wrapper">
    <div class="nav-tabs-content justify-center">
      <div
        v-for="(tab, idx) in tabs"
        :key="tab.value"
        class="nav-tab"
        :class="{ active: tab.value === activeTab }"
        :style="{ width: tabWidth + 'px' }"
        @click="setActiveTab(tab.value)"
      >
        <div class="nav-tab-background">
          <svg class="nav-tab-before" width="7" height="7">
            <path d="M 0 7 A 7 7 0 0 0 7 0 L 7 7 Z"></path>
          </svg>
          <div class="nav-tab-content"></div>
          <svg class="nav-tab-after" width="7" height="7">
            <path d="M 0 0 A 7 7 0 0 0 7 7 L 0 7 Z"></path>
          </svg>
        </div>
        <div class="nav-tab-label">{{ tab.label }}</div>
      </div>
    </div>
    <div class="bg-white rounded-b-lg -mt-[1px] relative z-0" style="border-top: none; border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
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
  tabs: {
    type: Array,
    default: () => [
      { label: 'Incoming', value: 'incoming' },
      { label: 'Outgoing', value: 'outgoing' },
    ],
  },
})
const emit = defineEmits(['update:activeTab'])

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

<style scoped>
.nav-tabs-wrapper {
  padding-top: 10px;
  background-color: #dee1e6;
  position: relative;
  z-index: 0;
}
.nav-tabs-content {
  height: 40px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  z-index: 0;
}
.nav-tab {
  height: 100%;
  display: flex;
  align-items: center;
  user-select: none;
  box-sizing: border-box;
  position: relative;
  margin-right: 24px;
  cursor: pointer;
  z-index: 0;
  transition: z-index 0.15s;
}
.nav-tab:last-child {
  margin-right: 0;
}
.nav-tab-background {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: flex-end;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 0;
}
.nav-tab-content {
  flex: 1;
  height: 100%;
  background: #f2f3f5;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  border: 1px solid #bfc4c9;
  border-bottom: none;
  transition: background 150ms;
}
.nav-tab.active .nav-tab-content {
  background: #fff;
  border-color: #bfc4c9;
}
.nav-tab-before,
.nav-tab-after {
  display: block;
  position: relative;
  z-index: 0;
  fill: #f2f3f5;
  transition: fill 150ms;
}
.nav-tab.active .nav-tab-before,
.nav-tab.active .nav-tab-after {
  fill: #fff;
}
.nav-tab-label {
  position: relative;
  z-index: 0;
  font-size: 1.25rem;
  font-weight: bold;
  color: #595959;
  margin: 0 24px;
  pointer-events: none;
  transition: color 150ms;
  white-space: nowrap;
}
.nav-tab.active .nav-tab-label {
  color: #222;
}
</style> 