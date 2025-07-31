<template>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200">
      <thead class="bg-gray-100">
        <tr>
          <th v-for="col in columns" :key="col.key" class="px-4 py-2 border font-semibold text-gray-700">
            {{ col.key === 'documentDate' ? 'Received By' : col.label }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, rowIndex) in rows" :key="rowIndex" :class="rowIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
          <td v-for="col in columns" :key="col.key" class="px-4 py-2 border">
            <slot :name="col.key" :row="row">
              {{ row[col.key] || '-' }}
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