<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-zinc-800 p-4">
            <div class="flex items-center">
                <div class="w-3 h-12 bg-zinc-800 rounded mr-3"></div>
                <div>
                    <p class="text-sm text-gray-600">Total Documents</p>
                    <p class="text-2xl font-bold text-gray-900">42</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-incoming p-4">
            <div class="flex items-center">
                <div class="w-3 h-12 bg-incoming rounded mr-3"></div>
                <div>
                    <p class="text-sm text-gray-600">Incoming</p>
                    <p class="text-2xl font-bold text-gray-900">7</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-outgoing p-4">
            <div class="flex items-center">
                <div class="w-3 h-12 bg-outgoing rounded mr-3"></div>
                <div>
                    <p class="text-sm text-gray-600">Outgoing</p>
                    <p class="text-2xl font-bold text-gray-900">1</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-pending p-4">
            <div class="flex items-center">
                <div class="w-3 h-12 bg-pending rounded mr-3"></div>
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">2</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

</script>