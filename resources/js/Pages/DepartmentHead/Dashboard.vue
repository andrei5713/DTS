<template>
  <div class="min-h-full p-4">
    <Header />
    <div class="mt-6">
      <Tabs v-model="activeTab" :tabs="departmentHeadTabs">
        <template #default="{ activeTab }">
          <div v-if="activeTab === 'outgoing'">
            <Outgoing />
          </div>
          <div v-else-if="activeTab === 'incoming'">
            <Incoming />
          </div>
        </template>
      </Tabs>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Header from '@/Components/Header.vue'
import Tabs from '@/Components/Tabs.vue'
import Outgoing from '../Outgoing.vue'
import Incoming from '../Incoming.vue'

const activeTab = ref('outgoing')
const departmentHeadTabs = [
  { label: 'Outgoing', value: 'outgoing' },
  { label: 'Incoming', value: 'incoming' },
]
</script>
