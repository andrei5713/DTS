<!-- Home.vue -->
<template>
    <div class="min-h-full p-4 bg-gray-200">
        <Header />
        <div class="mt-6">
            <Tabs v-model="activeTab">
                <template #default="{ activeTab }">
                    <div v-if="activeTab === 'incoming'">
                        <Incoming />
                    </div>
                    <div v-else-if="activeTab === 'outgoing'">
                        <Outgoing />
                    </div>
                </template>
            </Tabs>
        </div>
    </div>
</template>

<script setup>
import Header from '../Components/Header.vue'
import Tabs from '../Components/Tabs.vue'
import Incoming from './Incoming.vue'
import Outgoing from './Outgoing.vue'
import { ref } from 'vue'

const activeTab = ref('incoming')
</script>