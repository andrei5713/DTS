<template>
  <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm bg-white">
    <table class="min-w-full divide-y divide-gray-200" style="overflow: hidden;">
      <thead class="bg-gray-100" style="overflow: hidden;">
        <tr>
          <th v-for="col in columns" :key="col.key" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="overflow: hidden; position: relative;">
            {{ col.key === 'documentDate' ? 'Received By' : col.label }}
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <tr v-for="(row, rowIndex) in rows" :key="rowIndex" :class="[rowIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50/50', 'hover:bg-blue-50/30 transition-colors duration-200']">
          <td v-for="col in columns" :key="col.key" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
            <slot :name="col.key" :row="row">
              <span class="text-gray-600">{{ row[col.key] || '-' }}</span>
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  columns: {
    type: Array,
    required: true
  },
  rows: {
    type: Array,
    required: true
  }
});
</script> 