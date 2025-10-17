<script setup>
import { ref, defineProps } from 'vue';
import { Mail, Lock, User } from 'lucide-vue-next';
import { useForm, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Slideshow from '@/Components/Slideshow.vue'

const currentMode = ref(true);
const showPassword = ref(false); // State for password visibility
const showPasswordConfirmation = ref(false); // State for password confirmation visibility
const form = useForm({
  name: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const props = defineProps({
  isLoginMode: {
    type: Boolean,
    default: true,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  status: {
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

const toggleShowPassword = () => {
  showPassword.value = !showPassword.value;
};

const toggleShowPasswordConfirmation = () => {
  showPasswordConfirmation.value = !showPasswordConfirmation.value;
};

const handleAuth = () => {
  if (currentMode.value) {
    form.post(route('login'), {
      onFinish: () => form.reset('password'),
      onError: () => {},
    });
  } else {
    form.post(route('register'), {
      onSuccess: () => {},
      onFinish: () => form.reset('password', 'password_confirmation'),
      onError: () => {},
    });
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
    <div class="w-full max-w-[1280px] grid grid-cols-1 md:grid-cols-[1.5fr_1fr] gap-6">
      <!-- Left: slideshow from public/images/slideshow -->
      <div class="bg-gray-100 rounded-2xl overflow-hidden">
        <Slideshow />
      </div>

      <!-- Right: auth card -->
      <div class="relative bg-white rounded-2xl shadow p-6 md:p-8 pt-10 md:pt-14">
        <!-- DocTrack logo in top left -->
        <div class="absolute top-4 left-4">
          <h1 class="text-[20px] font-extrabold tracking-tight">
            <span class="text-blue-600">Doc</span><span class="text-red-600">Track</span>
          </h1>
        </div>

        <div class="flex items-center justify-end mb-6">
          <div class="text-xs text-gray-500">
            <span v-if="currentMode">Don't have an account?</span>
            <span v-else>Already have an account?</span>
            <a href="#" @click.prevent="toggleMode" class="ml-1 inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold border border-transparent bg-gray-800 text-white hover:bg-gray-900">{{ currentMode ? 'Sign up' : 'Login' }}</a>
          </div>
        </div>

        <div class="mb-4 text-center mt-8 md:mt-10">
          <p class="text-[12px] text-gray-600">Please enter your login details below.</p>
        </div>

        <form @submit.prevent="handleAuth" class="space-y-4 mt-8 md:mt-10">
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
              :type="showPassword ? 'text' : 'password'"
              autocomplete="current-password"
              required
              v-model="form.password"
              class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="••••••••"
            />
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
              <button
                type="button"
                @click="toggleShowPassword"
                class="focus:outline-none"
              >
                <Eye v-if="!showPassword" class="h-5 w-5 text-gray-400" />
                <EyeOff v-else class="h-5 w-5 text-gray-400" />
              </button>
            </div>
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
              :type="showPasswordConfirmation ? 'text' : 'password'"
              required
              v-model="form.password_confirmation"
              class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
              placeholder="••••••••"
            />
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
              <button
                type="button"
                @click="toggleShowPasswordConfirmation"
                class="focus:outline-none"
              >
                <Eye v-if="!showPasswordConfirmation" class="h-5 w-5 text-gray-400" />
                <EyeOff v-else class="h-5 w-5 text-gray-400" />
              </button>
            </div>
          </div>
          <div v-if="form.errors.password_confirmation" class="text-sm text-red-600 mt-1">{{ form.errors.password_confirmation }}</div>
        </div>

        <div class="flex items-center justify-between text-sm" v-if="currentMode">
          <label class="inline-flex items-center gap-2 select-none">
            <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-gray-800 focus:ring-gray-700" />
            <span class="text-gray-700">Remember me</span>
          </label>
          <Link :href="route('password.request')" class="text-gray-700 hover:text-gray-900">Forgot Your Password?</Link>
        </div>

        <button type="submit" :disabled="form.processing" class="w-full inline-flex items-center justify-center rounded-md bg-gray-800 text-white text-sm font-semibold py-2.5 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 disabled:opacity-60">
          <span v-if="!form.processing">{{ currentMode ? 'Login' : 'Sign up' }}</span>
          <span v-else>{{ currentMode ? 'Signing in...' : 'Signing up...' }}</span>
        </button>
        </form>

        <div class="relative my-2">
          <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
          <div class="relative flex justify-center text-xs"><span class="bg-white px-2 text-gray-500">OR</span></div>
        </div>

        <a href="/auth/google" class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 bg-white py-2.5 text-sm font-semibold text-gray-800 hover:bg-gray-50">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12 s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C33.232,6.053,28.851,4,24,4C12.955,4,4,12.955,4,24 s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,16.108,18.961,13,24,13c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657 C33.232,6.053,28.851,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c4.778,0,9.135-1.837,12.441-4.828l-5.754-4.869C28.614,35.623,26.409,36,24,36 c-5.202,0-9.619-3.317-11.274-7.955l-6.548,5.047C9.592,39.556,16.268,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.097,5.571c0.001-0.001,0.002-0.001,0.003-0.002 l6.581,4.938C36.725,39.935,44,35,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
          <span>Sign in with Google</span>
        </a>

        <p class="mt-6 text-center text-xs text-gray-500">© 2025 National Food Authority - Quezon City Branch. All Rights Reserved.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* No specific styles needed beyond Tailwind CSS */
</style>