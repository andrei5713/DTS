<script setup>
import { computed } from 'vue';
import { AlertCircle, Eye, EyeClosed } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    id: {
        type: String,
        default: () => `input-${Math.random().toString(36).substring(2, 9)}`
    }
});

const emit = defineEmits(['update:modelValue']);
const showPassword = ref(false);
const hasError = computed(() => !!props.error);
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
    <div>
        <label v-if="label" :for="id" class="block text-sm font-medium mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="relative">
            <input v-if="type !== 'textarea'" :id="id"
                :type="type === 'password' ? (showPassword ? 'text' : 'password') : type" :value="modelValue"
                :placeholder="placeholder" :disabled="disabled" :required="required" @input="updateValue"
                class="w-full px-4 py-2 border rounded-lg outline-none transition-colors dark:bg-dark dark:text-light dark:border-slate-700"
                :class="[
                    hasError
                        ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-transparent'
                        : 'border-slate-300 focus:ring-2 focus:ring-purple-600 focus:border-transparent'
                ]" />
            <textarea v-else :id="id" :value="modelValue" :placeholder="placeholder" :disabled="disabled"
                :required="required" @input="updateValue"
                class="w-full px-4 py-2 border rounded-lg outline-none transition-colors dark:bg-dark dark:text-light dark:border-slate-700" />
            <button v-if="type === 'password'" type="button" @click="togglePasswordVisibility"
                class="absolute inset-y-0 right-0 pr-3 flex items-center hover:">
                <Eye v-if="showPassword" class="h-5 w-5 text-purple-600" />
                <EyeClosed v-else class="h-5 w-5 text-purple-600" />
            </button>
        </div>

        <div v-if="hasError" class="mt-1 flex items-center text-red-500 text-sm">
            <AlertCircle class="h-4 w-4 mr-1" />
            <span>{{ error }}</span>
        </div>
    </div>
</template>