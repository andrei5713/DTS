<template>
    <div class="min-h-full p-4">
        <div class="flex w-full max-w-7xl bg-white ">

            <!-- Left: 2/3 background image with overlay stats -->
            <div class="hidden lg:flex w-3/5 relative rounded-2xl overflow-hidden">
                <!-- Background Image -->
                <img src="../Images/placeholder.jpg" alt="Workspace"
                    class="absolute inset-0 w-full h-full object-cover" />
            </div>

            <!-- Right: 1/3 Login Form -->
            <div class="w-full lg:w-2/5 pt-8 flex items-center justify-center bg-white">
                <div class="w-full max-w-sm">
                    <!-- Top right link -->
                    <div class="text-right text-sm text-zinc-800 pb-24">
                        Don't have an account?
                        <a href="#" class="btn-primary-2 ml-2">Sign up</a>
                    </div>

                    <!-- Form header -->
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-blue-700">
                            Doc<span class="text-red-600">Track</span>
                        </h2>
                        <p class="text-sm text-gray-500 mt-2 mb-4">
                            Please enter your login details below.
                        </p>
                    </div>

                    <!-- Login form -->
                    <form @submit.prevent="handleLogin" class="space-y-4">
                        <input v-model="form.email" type="email" placeholder="Email Address" class="input-field"
                            required>

                        <input v-model="form.password" type="password" placeholder="Password" class="input-field"
                            required>

                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <input id="checkbox" type="checkbox" value="" class="w-4 h-4">
                                <label for="checkbox" class="ms-2 text-sm font-medium">Remember me</label>
                            </div>
                            <a href="#" class="hyper-link">
                                Forgot Your Password?
                            </a>
                        </div>

                        <button type="submit" class="btn-primary w-full">
                            Login
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="flex items-center gap-4 my-4">
                        <div class="flex-1 h-px bg-gray-300"></div>
                        <div class="text-sm text-gray-500">OR</div>
                        <div class="flex-1 h-px bg-gray-300"></div>
                    </div>

                    <!-- Google login -->
                    <button @click="loginWithGoogle"
                        class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
                            class="w-5 h-5 mr-3" />
                        <span class="text-gray-700 font-medium">Sign in with Google</span>
                    </button>
                </div>
            </div>
        </div>

        <footer class="w-full text-center text-sm text-gray-500 mt-8">
            &copy; 2025 National Food Authority - Quezon City Branch. All Rights Reserved.
        </footer>
    </div>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const form = reactive({
    email: '',
    password: ''
})

const handleLogin = () => {
    router.post('/login', form)
}

const loginWithGoogle = () => {
    window.location.href = '/auth/google';
}

</script>