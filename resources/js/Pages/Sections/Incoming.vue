<script setup>
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
</script>
<template>
    <div class="min-h-screen bg-gray-50">
        <Header />

        <div class="px-6 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-1">
                    <Sidebar :active-section="activeSection" @section-change="handleSectionChange" />
                </div>
            </div>
        </div>
    </div>
</template>