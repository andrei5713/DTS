<template>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-lg font-bold mb-4">Bar Chart</h2>
        <apexchart width="800" height="400" type="bar" :options="options" :series="series"></apexchart>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const series = ref([
    {
        name: 'Incoming',
        data: [44, 55, 57, 56, 61, 58, 63],
    },
    {
        name: 'Outgoing',
        data: [76, 85, 100, 98, 87, 100, 91],
    },
    {
        name: 'Pending',
        data: [35, 41, 36, 26, 45, 48, 52],
    },
])

const options = ref({
    colors: ['#15b972', '#fa832f', '#047cf9'],
    chart: {
        type: 'bar',
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '50%',
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent'],
    },
    xaxis: {
        categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    },
    yaxis: {
        max: 100,
    },
    fill: {
        opacity: 1,
    },
})
</script>