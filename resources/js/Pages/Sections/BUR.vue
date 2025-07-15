<script setup>
import { ref } from 'vue'
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';

const activeSection = ref('bur');

function handleSectionChange(newSection) {
    activeSection.value = newSection;
}

</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <!-- Header at the top -->
        <Header />

        <!-- Sidebar + Main -->
        <div class="flex flex-1 p-6 ">
            <!-- Sidebar (left) -->
            <Sidebar :active-section="activeSection" @section-change="handleSectionChange" />

            <!-- Main Content (right) -->
            <main class="flex-1 ml-6 overflow-auto">

                <!-- Placeholder content -->
                <div class="bg-white p-6 rounded shadow">
                    <h1 class="text-2xl font-bold">BUR Section</h1>
                    <p>...</p>
                </div>
            </main>
        </div>
    </div>
</template>
