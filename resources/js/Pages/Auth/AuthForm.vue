<script setup>
import { ref, defineProps } from 'vue';
import { Mail, Lock, User } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const currentMode = ref(true);
const form = useForm({
  name: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '', // Added for registration
});

const props = defineProps({
  isLoginMode: {
    type: Boolean,
    default: true,
  },
  errors: { // Inertia automatically passes validation errors here
    type: Object,
    default: () => ({}),
  },
  status: { // Inertia automatically passes flash messages here
    type: String,
    default: '',
  },
});

// Initialize currentMode based on prop from Laravel controller
currentMode.value = props.isLoginMode;

const toggleMode = () => {
  currentMode.value = !currentMode.value;
  form.reset(); // Clear form fields and errors when switching
  form.clearErrors();
};

const handleAuth = () => {
  if (currentMode.value) {
    form.post(route('login'), {
      onFinish: () => form.reset('password'),
      onError: () => {
        // Errors are automatically handled by useForm and passed to props.errors
      },
    });
  } else {
    form.post(route('register'), {
      onSuccess: () => {
        // Laravel will redirect and flash a status message
      },
      onFinish: () => form.reset('password', 'password_confirmation'),
      onError: () => {
        // Errors are automatically handled by useForm and passed to props.errors
      },
    });
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8 space-y-6">
      <h2 class="text-3xl font-bold text-center text-gray-900">{{ currentMode ? 'Login' : 'Sign Up' }}</h2>
      <p class="text-center text-gray-600">
        {{ currentMode ? 'Sign in to your account' : 'Create your new account' }}
      </p>

      <form @submit.prevent="handleAuth" class="space-y-4">
        <div v-if="!currentMode">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <User class="h-5 w-5 text-gray-400" />
            </div>
            <input
              id="name"
              name="name"
              type="text"
              autocomplete="name"
              required
              v-model="form.name"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="Full Name"
            />
          </div>
          <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
        </div>

        <div v-if="!currentMode">
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <User class="h-5 w-5 text-gray-400" />
            </div>
            <input
              id="username"
              name="username"
              type="text"
              autocomplete="username"
              required
              v-model="form.username"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="Username"
            />
          </div>
          <div v-if="form.errors.username" class="text-sm text-red-600 mt-1">{{ form.errors.username }}</div>
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Mail class="h-5 w-5 text-gray-400" />
            </div>
            <input
              id="email"
              name="email"
              type="email"
              autocomplete="email"
              required
              v-model="form.email"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="you@example.com"
            />
          </div>
          <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Lock class="h-5 w-5 text-gray-400" />
            </div>
            <input
              id="password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              v-model="form.password"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="••••••••"
            />
          </div>
          <div v-if="form.errors.password" class="text-sm text-red-600 mt-1">{{ form.errors.password }}</div>
        </div>

        <div v-if="!currentMode">
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
            <span v-if="!form.processing">{{ currentMode ? 'Sign in' : 'Sign up' }}</span>
            <span v-else>{{ currentMode ? 'Signing in...' : 'Signing up...' }}</span>
          </button>
        </div>
      </form>

      <div class="text-center text-sm text-gray-600">
        {{ currentMode ? "Don't have an account?" : "Already have an account?" }}
        <a href="#" @click.prevent="toggleMode" class="font-medium text-gray-800 hover:text-gray-900">
          {{ currentMode ? 'Sign up' : 'Login' }}
        </a>
      </div>

      <div v-if="currentMode" class="text-center text-sm">
        <a :href="route('password.request')" class="font-medium text-gray-800 hover:text-gray-900">
          Forgot your password?
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* No specific styles needed beyond Tailwind CSS */
</style>
