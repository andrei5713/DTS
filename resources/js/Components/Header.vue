<template>
    <header class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center space-x-4">
                <img src="@/Images/NFA.svg" class="w-12 h-12">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">NATIONAL FOOD AUTHORITY</h1>
                    <p class="text-sm text-gray-600">DOCUMENT TRACKING SYSTEM</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <Bell class="w-6 h-6 text-gray-600 cursor-pointer" />
                <div class="w-8 h-8 bg-orange-500 rounded-full cursor-pointer"></div>
            </div>
        </div>
    </header>
</template>

<script setup>
import {
    Bell,
} from 'lucide-vue-next'
</script>