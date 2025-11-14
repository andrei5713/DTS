<template>
  <div class="rounded-lg border border-gray-200 shadow-sm bg-white overflow-hidden">
    <div class="overflow-x-auto max-w-full">
      <table class="w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th v-for="col in columns" :key="col.key" class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">
              {{ col.key === 'documentDate' ? 'Received By' : col.label }}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="(row, rowIndex) in rows" :key="rowIndex" :class="[rowIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50/50', 'hover:bg-blue-50/30 transition-colors duration-200']">
            <td v-for="col in columns" :key="col.key" class="px-3 py-4 text-sm font-medium text-gray-800" :class="col.key === 'ACTIONS' || col.key === 'duration' ? 'whitespace-nowrap' : 'max-w-xs'">
              <div v-if="col.key !== 'ACTIONS' && col.key !== 'duration'" class="truncate" :title="typeof row[col.key] === 'string' ? row[col.key] : ''">
                <slot :name="col.key" :row="row">
                  <span class="text-gray-600">{{ row[col.key] || '-' }}</span>
                </slot>
              </div>
              <div v-else>
                <slot :name="col.key" :row="row">
                  <span class="text-gray-600">{{ row[col.key] || '-' }}</span>
                </slot>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
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