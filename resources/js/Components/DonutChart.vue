<template>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-lg font-bold mb-4">Donut Chart</h2>
        <apexchart width="800" height="400" type="donut" :options="options" :series="series" />
    </div>
</template>

<script setup>
import { ref } from 'vue'

const series = ref([127, 247, 374]) // Incoming, Pending, Outgoing

const options = ref({
    labels: ['Incoming', 'Outgoing', 'Pending'],
    colors: ['#15b972', '#fa832f', '#047cf9'],
    legend: {
        position: 'bottom'
    },
    dataLabels: {
        enabled: true
    }
})
</script>