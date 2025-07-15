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
                <button class="relative">
                    <Bell class="w-6 h-6" />
                </button>

                <div class="relative">
                    <button @click="showLogin" class="flex w-fit items-center gap-2 profile-btn">
                        <div class="font-semibold">CPMSD</div>
                        <ChevronDown class="w-4 h-4" />
                    </button>
                    <div v-if="isShowLogin"
                        class="absolute bg-light shadow-lg p-2 z-10 rounded-md bottom-0 translate-y-8 profile-dropdown">
                        <Link :href="route('logout')" method="post">Log out</Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import {
    Bell, ChevronDown
} from 'lucide-vue-next'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from '../../../vendor/tightenco/ziggy/src/js'

const isShowLogin = ref(false)
const showLogin = () => {
    isShowLogin.value = !isShowLogin.value;
}

const showNotifications = ref(false)
const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
    if (showNotifications.value) {
        isShowLogin.value = false;
    }
}
</script>