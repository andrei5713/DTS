<script setup>
import { ref, defineProps } from 'vue';
import { Lock } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
  token: {
    type: String,
    required: true
  },
  email: {
    type: String,
    required: true
  },
  status: { // Inertia automatically passes flash messages here
    type: String,
    default: '',
  },
  errors: { // Inertia automatically passes validation errors here
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const handleResetPassword = () => {
  form.post(route('password.update'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
    onError: () => {
      // Errors are automatically handled by useForm and passed to props.errors
    },
  });
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8 space-y-6">
      <h2 class="text-3xl font-bold text-center text-gray-900">Reset Password</h2>
      <p class="text-center text-gray-600">Set your new password.</p>

      <form @submit.prevent="handleResetPassword" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Lock class="h-5 w-5 text-gray-400" />
            </div>
            <input
              id="email"
              name="email"
              type="email"
              required
              v-model="form.email"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="you@example.com"
              readonly
            />
          </div>
          <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Lock class="h-5 w-5 text-gray-400" />
            </div>
            <input
              id="password"
              name="password"
              type="password"
              required
              v-model="form.password"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="••••••••"
            />
          </div>
          <div v-if="form.errors.password" class="text-sm text-red-600 mt-1">{{ form.errors.password }}</div>
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Lock class="h-5 w-5 text-gray-400" />
            </div>
            <input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              required
              v-model="form.password_confirmation"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="••••••••"
            />
          </div>
          <div v-if="form.errors.password_confirmation" class="text-sm text-red-600 mt-1">{{ form.errors.password_confirmation }}</div>
        </div>

        <div v-if="props.status" class="text-sm text-green-600 text-center">
          {{ props.status }}
        </div>

        <div>
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!form.processing">Reset Password</span>
            <span v-else>Resetting...</span>
          </button>
        </div>
      </form>

      <div class="text-center text-sm text-gray-600">
        <a :href="route('login')" class="font-medium text-gray-800 hover:text-gray-900">Back to Login</a>
      </div>
    </div>
  </div>
</template>
