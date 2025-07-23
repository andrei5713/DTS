<!-- resources/js/Components/LoginCarousel.vue -->
<template>
    <div class="w-full h-full relative overflow-hidden rounded-lg">
        <transition-group name="fade" mode="out-in">
            <img v-for="(image, index) in [images[currentIndex]]" :key="image" :src="image"
                class="w-full h-full object-cover absolute inset-0 transition-opacity duration-700"
                @load="loaded = true" />
            <div v-if="!loaded" class="text-white">Loading...</div>
        </transition-group>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const images = [
    '/images/nfa-1.jpg',
    '/images/nfa-2.webp',
    '/images/nfa-3.jpg',
    '/images/nfa-4.jpg',
    '/images/nfa-5.jpg',
];

const currentIndex = ref(0);

onMounted(() => {
    setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % images.length;
    }, 3000); // Change every 3 seconds
});

const loaded = ref(false);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>